<?php

class QuoteController extends Controller
{
    private Quote $quotes;
    private Client $clients;
    private Product $products;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->quotes = new Quote();
        $this->clients = new Client();
        $this->products = new Product();
    }

    public function index(): void
    {
        $this->render('quotes/index', [
            'pageTitle' => 'Orcamentos',
            'quotes' => $this->quotes->all(),
        ]);
    }

    public function create(): void
    {
        $products = $this->products->all();
        $productsJson = array_map(function (array $product): array {
            return [
                'id' => (int) $product['id'],
                'name' => $product['name'],
                'description' => $product['description'],
                'unit_price' => (float) $product['unit_price'],
                'cost_price' => (float) ($product['cost_price'] ?? 0),
            ];
        }, $products);

        $this->render('quotes/form', [
            'pageTitle' => 'Novo Orcamento',
            'clients' => $this->clients->all(),
            'products' => $products,
            'productsJson' => dashboard_json($productsJson),
        ]);
    }

    public function store(): void
    {
        if (!is_post()) {
            redirect(route_url(['page' => 'quotes']));
        }

        $clientId = (int) ($_POST['client_id'] ?? 0);
        $notes = trim($_POST['notes'] ?? '');
        with_old_input([
            'client_id' => $clientId,
            'notes' => $notes,
        ]);
        $client = $this->clients->find($clientId);

        if (!$client) {
            flash('error', 'Selecione um cliente valido.');
            redirect(route_url(['page' => 'quotes', 'action' => 'create']));
        }

        $itemsData = $this->buildItems($_POST['items'] ?? []);

        if (isset($itemsData['error'])) {
            flash('error', $itemsData['error']);
            redirect(route_url(['page' => 'quotes', 'action' => 'create']));
        }

        $quoteId = $this->quotes->createQuote($clientId, $notes, $itemsData);

        flash('success', 'Orcamento criado com sucesso. Agora voce ja pode gerar o PDF.');
        redirect(route_url(['page' => 'quotes', 'action' => 'show', 'id' => $quoteId]));
    }

    public function show(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $quote = $this->quotes->findWithItems($id);

        if (!$quote) {
            flash('error', 'Orcamento nao encontrado.');
            redirect(route_url(['page' => 'quotes']));
        }

        $this->render('quotes/show', [
            'pageTitle' => 'Detalhes do Orcamento',
            'quote' => $quote,
            'statusOptions' => status_select_options(),
            'pdfUrl' => quote_pdf_url($quote),
            'whatsAppUrl' => quote_whatsapp_url($quote),
        ]);
    }

    public function updateStatus(): void
    {
        if (!is_post()) {
            redirect(route_url(['page' => 'quotes']));
        }

        $id = (int) ($_GET['id'] ?? 0);
        $status = $_POST['status'] ?? 'enviado';
        $quote = $this->quotes->find($id);

        if (!$quote) {
            flash('error', 'Orcamento nao encontrado.');
            redirect(route_url(['page' => 'quotes']));
        }

        if (!array_key_exists($status, status_select_options())) {
            flash('error', 'Status invalido.');
            redirect(route_url(['page' => 'quotes', 'action' => 'show', 'id' => $id]));
        }

        $this->quotes->updateStatus($id, $status);
        flash('success', 'Status atualizado com sucesso.');
        redirect(route_url(['page' => 'quotes', 'action' => 'show', 'id' => $id]));
    }

    public function generatePdf(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $quote = $this->quotes->findWithItems($id);

        if (!$quote) {
            flash('error', 'Orcamento nao encontrado.');
            redirect(route_url(['page' => 'quotes']));
        }

        $autoload = __DIR__ . '/../vendor/dompdf/autoload.inc.php';
        if (!is_file($autoload)) {
            flash('error', 'A biblioteca dompdf nao foi encontrada no projeto.');
            redirect(route_url(['page' => 'quotes', 'action' => 'show', 'id' => $id]));
        }

        require_once $autoload;

        $logoPath = public_path(ltrim($this->config['company']['logo'], '/'));
        $logoData = '';
        if (is_file($logoPath)) {
            $extension = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
            $mime = $extension === 'svg' ? 'image/svg+xml' : 'image/' . $extension;
            $logoData = 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($logoPath));
        }

        ob_start();
        $company = $this->config['company'];
        $statusMeta = quote_status_meta($quote['status']);
        require __DIR__ . '/../views/quotes/pdf.php';
        $html = ob_get_clean();

        $options = new Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf\Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfDirectory = __DIR__ . '/../pdf';
        if (!is_dir($pdfDirectory)) {
            mkdir($pdfDirectory, 0775, true);
        }

        $fileName = 'orcamento-' . $quote['id'] . '.pdf';
        $relativePath = trim($this->config['app']['public_pdf_path'], '/') . '/' . $fileName;
        file_put_contents($pdfDirectory . '/' . $fileName, $dompdf->output());

        $this->quotes->savePdfPath($quote['id'], $relativePath);

        flash('success', 'PDF gerado com sucesso. Agora voce pode abrir o arquivo ou enviar por WhatsApp.');
        redirect(route_url(['page' => 'quotes', 'action' => 'show', 'id' => $quote['id']]));
    }

    private function buildItems(array $rawItems): array
    {
        $items = [];

        foreach ($rawItems as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $quantity = (float) str_replace(',', '.', $item['quantity'] ?? 0);
            $unitPrice = (float) str_replace(',', '.', $item['unit_price'] ?? 0);
            $description = trim($item['item_description'] ?? '');

            if ($productId <= 0 || $quantity <= 0 || $unitPrice <= 0) {
                continue;
            }

            $product = $this->products->find($productId);

            if (!$product) {
                return ['error' => 'Um dos produtos selecionados nao existe mais.'];
            }

            $items[] = [
                'product_id' => $productId,
                'item_name' => $product['name'],
                'item_description' => $description !== '' ? $description : $product['description'],
                'unit_price' => $unitPrice,
                'cost_price' => (float) ($product['cost_price'] ?? 0),
                'quantity' => $quantity,
                'total' => $unitPrice * $quantity,
            ];
        }

        if ($items === []) {
            return ['error' => 'Adicione pelo menos um produto ou servico ao orcamento.'];
        }

        return $items;
    }
}
