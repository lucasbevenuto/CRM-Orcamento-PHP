<?php

class ClientController extends Controller
{
    private Client $clients;
    private Quote $quotes;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->clients = new Client();
        $this->quotes = new Quote();
    }

    public function index(): void
    {
        $search = trim($_GET['q'] ?? '');

        $this->render('clients/index', [
            'pageTitle' => 'Clientes',
            'clients' => $this->clients->all($search),
            'search' => $search,
        ]);
    }

    public function create(): void
    {
        $this->render('clients/form', [
            'pageTitle' => 'Novo Cliente',
            'client' => [
                'name' => '',
                'email' => '',
                'phone' => '',
                'company' => '',
                'notes' => '',
            ],
            'formAction' => route_url(['page' => 'clients', 'action' => 'store']),
        ]);
    }

    public function store(): void
    {
        if (!is_post()) {
            redirect(route_url(['page' => 'clients']));
        }

        $data = $this->validatedData();

        if (isset($data['error'])) {
            flash('error', $data['error']);
            with_old_input($_POST);
            redirect(route_url(['page' => 'clients', 'action' => 'create']));
        }

        $this->clients->create($data);
        flash('success', 'Cliente cadastrado com sucesso.');
        redirect(route_url(['page' => 'clients']));
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $client = $this->clients->find($id);

        if (!$client) {
            flash('error', 'Cliente não encontrado.');
            redirect(route_url(['page' => 'clients']));
        }

        $this->render('clients/form', [
            'pageTitle' => 'Editar Cliente',
            'client' => $client,
            'formAction' => route_url(['page' => 'clients', 'action' => 'update', 'id' => $id]),
        ]);
    }

    public function update(): void
    {
        if (!is_post()) {
            redirect(route_url(['page' => 'clients']));
        }

        $id = (int) ($_GET['id'] ?? 0);
        $client = $this->clients->find($id);

        if (!$client) {
            flash('error', 'Cliente não encontrado.');
            redirect(route_url(['page' => 'clients']));
        }

        $data = $this->validatedData();

        if (isset($data['error'])) {
            flash('error', $data['error']);
            with_old_input($_POST);
            redirect(route_url(['page' => 'clients', 'action' => 'edit', 'id' => $id]));
        }

        $this->clients->update($id, $data);
        flash('success', 'Cliente atualizado com sucesso.');
        redirect(route_url(['page' => 'clients']));
    }

    public function delete(): void
    {
        if (!is_post()) {
            redirect(route_url(['page' => 'clients']));
        }

        $id = (int) ($_GET['id'] ?? 0);

        if ($this->quotes->countByClient($id) > 0) {
            flash('error', 'Este cliente possui orçamentos vinculados e não pode ser removido.');
            redirect(route_url(['page' => 'clients']));
        }

        $this->clients->delete($id);
        flash('success', 'Cliente removido com sucesso.');
        redirect(route_url(['page' => 'clients']));
    }

    public function history(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $client = $this->clients->find($id);

        if (!$client) {
            flash('error', 'Cliente não encontrado.');
            redirect(route_url(['page' => 'clients']));
        }

        $this->render('clients/history', [
            'pageTitle' => 'Histórico do Cliente',
            'client' => $client,
            'quotes' => $this->clients->quoteHistory($id),
        ]);
    }

    private function validatedData(): array
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $company = trim($_POST['company'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        if ($name === '' || $phone === '') {
            return ['error' => 'Nome e telefone são obrigatórios.'];
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['error' => 'Informe um e-mail válido.'];
        }

        return compact('name', 'email', 'phone', 'company', 'notes');
    }
}
