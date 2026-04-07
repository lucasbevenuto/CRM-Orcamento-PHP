<?php

class SettingsController extends Controller
{
    private Setting $settings;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->settings = new Setting();
    }

    public function index(): void
    {
        $this->render('settings/index', [
            'pageTitle' => 'Configuracoes da Empresa',
            'settings' => $this->settings->company(),
        ]);
    }

    public function update(): void
    {
        if (!is_post()) {
            redirect(route_url(['page' => 'settings']));
        }

        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'document' => trim($_POST['document'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'logo' => trim($_POST['current_logo'] ?? app_config('company.logo')),
        ];

        if ($data['name'] === '' || $data['phone'] === '' || $data['email'] === '' || $data['address'] === '') {
            flash('error', 'Preencha nome, telefone, email e endereco da empresa.');
            with_old_input($_POST);
            redirect(route_url(['page' => 'settings']));
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Informe um email valido para a empresa.');
            with_old_input($_POST);
            redirect(route_url(['page' => 'settings']));
        }

        if (!empty($_FILES['logo']['name'])) {
            $upload = $this->handleLogoUpload($_FILES['logo']);

            if (isset($upload['error'])) {
                flash('error', $upload['error']);
                with_old_input($_POST);
                redirect(route_url(['page' => 'settings']));
            }

            $data['logo'] = $upload['path'];
        }

        $this->settings->saveCompany($data);
        flash('success', 'Dados da empresa atualizados com sucesso.');
        redirect(route_url(['page' => 'settings']));
    }

    private function handleLogoUpload(array $file): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['error' => 'Nao foi possivel enviar a imagem da empresa.'];
        }

        $allowedExtensions = ['png', 'jpg', 'jpeg', 'svg', 'webp'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions, true)) {
            return ['error' => 'Formato de logo invalido. Use PNG, JPG, SVG ou WEBP.'];
        }

        if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
            return ['error' => 'A logo deve ter no maximo 2 MB.'];
        }

        $relativePath = 'uploads/company/logo-' . time() . '.' . $extension;
        $destination = public_path($relativePath);

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['error' => 'Falha ao salvar a logo enviada.'];
        }

        return ['path' => $relativePath];
    }
}
