<?php

class WhatsAppController extends Controller
{
    private Client $clients;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->clients = new Client();
    }

    public function index(): void
    {
        $search = trim($_GET['q'] ?? '');

        $this->render('whatsapp/index', [
            'pageTitle' => 'Diretorio de WhatsApp',
            'contacts' => $this->clients->whatsappDirectory($search),
            'search' => $search,
        ]);
    }
}
