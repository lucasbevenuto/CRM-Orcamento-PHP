<?php

class Quote extends BaseModel
{
    public function stats(): array
    {
        $stmt = $this->db->query(
            "SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN status = 'aprovado' THEN 1 ELSE 0 END) AS approved,
                SUM(CASE WHEN status = 'recusado' THEN 1 ELSE 0 END) AS rejected,
                SUM(CASE WHEN status = 'enviado' THEN 1 ELSE 0 END) AS sent
             FROM quotes"
        );

        $stats = $stmt->fetch() ?: [];

        return [
            'total' => (int) ($stats['total'] ?? 0),
            'approved' => (int) ($stats['approved'] ?? 0),
            'rejected' => (int) ($stats['rejected'] ?? 0),
            'sent' => (int) ($stats['sent'] ?? 0),
        ];
    }

    public function all(): array
    {
        $stmt = $this->db->query(
            'SELECT q.*, c.name AS client_name, c.phone AS client_phone
             FROM quotes q
             INNER JOIN clients c ON c.id = q.client_id
             ORDER BY q.created_at DESC'
        );

        return $stmt->fetchAll();
    }

    public function countByClient(int $clientId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM quotes WHERE client_id = :client_id');
        $stmt->execute(['client_id' => $clientId]);

        return (int) $stmt->fetchColumn();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT q.*, c.name AS client_name, c.email AS client_email, c.phone AS client_phone, c.company AS client_company, c.notes AS client_notes
             FROM quotes q
             INNER JOIN clients c ON c.id = q.client_id
             WHERE q.id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $quote = $stmt->fetch();

        return $quote ?: null;
    }

    public function items(int $quoteId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM quote_items WHERE quote_id = :quote_id ORDER BY id ASC');
        $stmt->execute(['quote_id' => $quoteId]);

        return $stmt->fetchAll();
    }

    public function findWithItems(int $id): ?array
    {
        $quote = $this->find($id);

        if (!$quote) {
            return null;
        }

        $quote['items'] = $this->items($id);

        return $quote;
    }

    public function createQuote(int $clientId, string $notes, array $items): int
    {
        $total = 0;
        foreach ($items as $item) {
            $total += $item['total'];
        }

        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                "INSERT INTO quotes (client_id, notes, total_amount, status, created_at, updated_at)
                 VALUES (:client_id, :notes, :total_amount, 'enviado', NOW(), NOW())"
            );
            $stmt->execute([
                'client_id' => $clientId,
                'notes' => $notes,
                'total_amount' => $total,
            ]);

            $quoteId = (int) $this->db->lastInsertId();
            $itemStmt = $this->db->prepare(
                'INSERT INTO quote_items (quote_id, product_id, item_name, item_description, unit_price, cost_price, quantity, total)
                 VALUES (:quote_id, :product_id, :item_name, :item_description, :unit_price, :cost_price, :quantity, :total)'
            );

            foreach ($items as $item) {
                $itemStmt->execute([
                    'quote_id' => $quoteId,
                    'product_id' => $item['product_id'],
                    'item_name' => $item['item_name'],
                    'item_description' => $item['item_description'],
                    'unit_price' => $item['unit_price'],
                    'cost_price' => $item['cost_price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['total'],
                ]);
            }

            $this->db->commit();

            return $quoteId;
        } catch (Throwable $exception) {
            $this->db->rollBack();
            throw $exception;
        }
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = $this->db->prepare('UPDATE quotes SET status = :status, updated_at = NOW() WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'status' => $status,
        ]);
    }

    public function savePdfPath(int $id, string $path): void
    {
        $stmt = $this->db->prepare('UPDATE quotes SET pdf_path = :pdf_path, updated_at = NOW() WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'pdf_path' => $path,
        ]);
    }

    public function dashboardSummary(): array
    {
        $stmt = $this->db->query(
            "SELECT
                COUNT(*) AS total_quotes,
                SUM(CASE WHEN status = 'aprovado' THEN 1 ELSE 0 END) AS approved_quotes,
                SUM(CASE WHEN status = 'enviado' THEN 1 ELSE 0 END) AS sent_quotes,
                SUM(CASE WHEN status = 'recusado' THEN 1 ELSE 0 END) AS rejected_quotes,
                COUNT(DISTINCT client_id) AS active_clients,
                COALESCE(SUM(CASE WHEN status = 'aprovado' THEN total_amount ELSE 0 END), 0) AS approved_revenue,
                COALESCE(SUM(CASE WHEN status = 'enviado' THEN total_amount ELSE 0 END), 0) AS pipeline_revenue,
                COALESCE(AVG(CASE WHEN status = 'aprovado' THEN total_amount END), 0) AS average_ticket
             FROM quotes"
        );
        $summary = $stmt->fetch() ?: [];

        $profitStmt = $this->db->query(
            "SELECT
                COALESCE(SUM(CASE WHEN q.status = 'aprovado' THEN (qi.total - (qi.cost_price * qi.quantity)) ELSE 0 END), 0) AS approved_profit,
                COALESCE(SUM(CASE WHEN q.status = 'enviado' THEN (qi.total - (qi.cost_price * qi.quantity)) ELSE 0 END), 0) AS pipeline_profit
             FROM quotes q
             INNER JOIN quote_items qi ON qi.quote_id = q.id"
        );
        $profit = $profitStmt->fetch() ?: [];

        $totalQuotes = (int) ($summary['total_quotes'] ?? 0);
        $approvedQuotes = (int) ($summary['approved_quotes'] ?? 0);

        return [
            'total_quotes' => $totalQuotes,
            'approved_quotes' => $approvedQuotes,
            'sent_quotes' => (int) ($summary['sent_quotes'] ?? 0),
            'rejected_quotes' => (int) ($summary['rejected_quotes'] ?? 0),
            'active_clients' => (int) ($summary['active_clients'] ?? 0),
            'approved_revenue' => (float) ($summary['approved_revenue'] ?? 0),
            'pipeline_revenue' => (float) ($summary['pipeline_revenue'] ?? 0),
            'average_ticket' => (float) ($summary['average_ticket'] ?? 0),
            'approved_profit' => (float) ($profit['approved_profit'] ?? 0),
            'pipeline_profit' => (float) ($profit['pipeline_profit'] ?? 0),
            'conversion_rate' => $totalQuotes > 0 ? round(($approvedQuotes / $totalQuotes) * 100, 1) : 0,
        ];
    }

    public function monthlyRevenueSeries(int $months = 6): array
    {
        $months = max(1, $months);
        $startDate = date('Y-m-01', strtotime('-' . ($months - 1) . ' months'));

        $rowsStmt = $this->db->prepare(
            "SELECT
                DATE_FORMAT(created_at, '%Y-%m') AS period_key,
                COUNT(*) AS quotes_count,
                COALESCE(SUM(CASE WHEN status = 'aprovado' THEN total_amount ELSE 0 END), 0) AS approved_revenue
             FROM quotes
             WHERE created_at >= :start_date
             GROUP BY DATE_FORMAT(created_at, '%Y-%m')
             ORDER BY period_key ASC"
        );
        $rowsStmt->execute(['start_date' => $startDate]);

        $profitStmt = $this->db->prepare(
            "SELECT
                DATE_FORMAT(q.created_at, '%Y-%m') AS period_key,
                COALESCE(SUM(CASE WHEN q.status = 'aprovado' THEN (qi.total - (qi.cost_price * qi.quantity)) ELSE 0 END), 0) AS approved_profit
             FROM quotes q
             INNER JOIN quote_items qi ON qi.quote_id = q.id
             WHERE q.created_at >= :start_date
             GROUP BY DATE_FORMAT(q.created_at, '%Y-%m')
             ORDER BY period_key ASC"
        );
        $profitStmt->execute(['start_date' => $startDate]);

        $revenueMap = [];
        foreach ($rowsStmt->fetchAll() as $row) {
            $revenueMap[$row['period_key']] = [
                'quotes_count' => (int) $row['quotes_count'],
                'approved_revenue' => (float) $row['approved_revenue'],
            ];
        }

        $profitMap = [];
        foreach ($profitStmt->fetchAll() as $row) {
            $profitMap[$row['period_key']] = (float) $row['approved_profit'];
        }

        $labels = [];
        $quotesCount = [];
        $revenue = [];
        $profit = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $periodKey = date('Y-m', strtotime('-' . $i . ' months'));
            $labels[] = date('M/y', strtotime($periodKey . '-01'));
            $quotesCount[] = $revenueMap[$periodKey]['quotes_count'] ?? 0;
            $revenue[] = $revenueMap[$periodKey]['approved_revenue'] ?? 0;
            $profit[] = $profitMap[$periodKey] ?? 0;
        }

        return [
            'labels' => $labels,
            'quotes' => $quotesCount,
            'revenue' => $revenue,
            'profit' => $profit,
        ];
    }

    public function statusBreakdown(): array
    {
        $stats = $this->stats();

        return [
            'Enviado' => $stats['sent'],
            'Aprovado' => $stats['approved'],
            'Recusado' => $stats['rejected'],
        ];
    }

    public function topClients(int $limit = 5): array
    {
        $limit = max(1, $limit);
        $stmt = $this->db->query(
            "SELECT c.name AS client_name,
                    COUNT(q.id) AS quotes_count,
                    COALESCE(SUM(CASE WHEN q.status = 'aprovado' THEN q.total_amount ELSE 0 END), 0) AS approved_revenue
             FROM clients c
             LEFT JOIN quotes q ON q.client_id = c.id
             GROUP BY c.id
             ORDER BY approved_revenue DESC, quotes_count DESC, c.name ASC
             LIMIT {$limit}"
        );

        return $stmt->fetchAll();
    }
}
