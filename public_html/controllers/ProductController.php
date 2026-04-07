<?php

class ProductController extends Controller
{
    private Product $products;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->products = new Product();
    }

    public function index(): void
    {
        $search = trim($_GET['q'] ?? '');

        $this->render('products/index', [
            'pageTitle' => 'Produtos e Servicos',
            'products' => $this->products->all($search),
            'search' => $search,
        ]);
    }

    public function create(): void
    {
        $this->render('products/form', [
            'pageTitle' => 'Novo Produto/Servico',
            'product' => [
                'name' => '',
                'description' => '',
                'unit_price' => '',
                'cost_price' => '',
            ],
            'formAction' => route_url(['page' => 'products', 'action' => 'store']),
        ]);
    }

    public function store(): void
    {
        if (!is_post()) {
            redirect(route_url(['page' => 'products']));
        }

        $data = $this->validatedData();

        if (isset($data['error'])) {
            flash('error', $data['error']);
            with_old_input($_POST);
            redirect(route_url(['page' => 'products', 'action' => 'create']));
        }

        $this->products->create($data);
        flash('success', 'Produto/servico cadastrado com sucesso.');
        redirect(route_url(['page' => 'products']));
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $product = $this->products->find($id);

        if (!$product) {
            flash('error', 'Produto/servico nao encontrado.');
            redirect(route_url(['page' => 'products']));
        }

        $this->render('products/form', [
            'pageTitle' => 'Editar Produto/Servico',
            'product' => $product,
            'formAction' => route_url(['page' => 'products', 'action' => 'update', 'id' => $id]),
        ]);
    }

    public function update(): void
    {
        if (!is_post()) {
            redirect(route_url(['page' => 'products']));
        }

        $id = (int) ($_GET['id'] ?? 0);
        $product = $this->products->find($id);

        if (!$product) {
            flash('error', 'Produto/servico nao encontrado.');
            redirect(route_url(['page' => 'products']));
        }

        $data = $this->validatedData();

        if (isset($data['error'])) {
            flash('error', $data['error']);
            with_old_input($_POST);
            redirect(route_url(['page' => 'products', 'action' => 'edit', 'id' => $id]));
        }

        $this->products->update($id, $data);
        flash('success', 'Produto/servico atualizado com sucesso.');
        redirect(route_url(['page' => 'products']));
    }

    public function delete(): void
    {
        if (!is_post()) {
            redirect(route_url(['page' => 'products']));
        }

        $id = (int) ($_GET['id'] ?? 0);
        $this->products->delete($id);
        flash('success', 'Produto/servico removido com sucesso.');
        redirect(route_url(['page' => 'products']));
    }

    private function validatedData(): array
    {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $unitPrice = (float) str_replace(',', '.', $_POST['unit_price'] ?? 0);
        $costPrice = (float) str_replace(',', '.', $_POST['cost_price'] ?? 0);

        if ($name === '' || $unitPrice <= 0) {
            return ['error' => 'Nome e preco unitario valido sao obrigatorios.'];
        }

        return [
            'name' => $name,
            'description' => $description,
            'unit_price' => $unitPrice,
            'cost_price' => max(0, $costPrice),
        ];
    }
}
