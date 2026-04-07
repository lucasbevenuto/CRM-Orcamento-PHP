<?php

class Client extends BaseModel
{
    public function all(string $search = ''): array
    {
        $sql = 'SELECT c.*, COUNT(q.id) AS quote_count
                FROM clients c
                LEFT JOIN quotes q ON q.client_id = c.id';
        $params = [];

        if ($search !== '') {
            $sql .= ' WHERE c.name LIKE :search';
            $params['search'] = '%' . $search . '%';
        }

        $sql .= ' GROUP BY c.id ORDER BY c.name ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM clients WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $client = $stmt->fetch();

        return $client ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO clients (name, email, phone, company, notes, created_at, updated_at)
             VALUES (:name, :email, :phone, :company, :notes, NOW(), NOW())'
        );

        $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'company' => $data['company'],
            'notes' => $data['notes'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE clients
             SET name = :name, email = :email, phone = :phone, company = :company, notes = :notes, updated_at = NOW()
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'company' => $data['company'],
            'notes' => $data['notes'],
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM clients WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function quoteHistory(int $clientId): array
    {
        $stmt = $this->db->prepare(
            'SELECT q.*, COUNT(qi.id) AS items_count
             FROM quotes q
             LEFT JOIN quote_items qi ON qi.quote_id = q.id
             WHERE q.client_id = :client_id
             GROUP BY q.id
             ORDER BY q.created_at DESC'
        );
        $stmt->execute(['client_id' => $clientId]);

        return $stmt->fetchAll();
    }

    public function whatsappDirectory(string $search = ''): array
    {
        $sql = 'SELECT c.*, COUNT(q.id) AS quotes_count, MAX(q.created_at) AS last_quote_at
                FROM clients c
                LEFT JOIN quotes q ON q.client_id = c.id';
        $params = [];

        if ($search !== '') {
            $sql .= ' WHERE c.name LIKE :search OR c.phone LIKE :search';
            $params['search'] = '%' . $search . '%';
        }

        $sql .= ' GROUP BY c.id ORDER BY c.name ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function monthlyRegistrations(int $months = 6): array
    {
        $months = max(1, $months);
        $startDate = date('Y-m-01', strtotime('-' . ($months - 1) . ' months'));

        $stmt = $this->db->prepare(
            'SELECT DATE_FORMAT(created_at, "%Y-%m") AS period_key, COUNT(*) AS total
             FROM clients
             WHERE created_at >= :start_date
             GROUP BY DATE_FORMAT(created_at, "%Y-%m")
             ORDER BY period_key ASC'
        );
        $stmt->execute(['start_date' => $startDate]);

        $rows = [];
        foreach ($stmt->fetchAll() as $row) {
            $rows[$row['period_key']] = (int) $row['total'];
        }

        return $rows;
    }
}
