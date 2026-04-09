<?php

class Setting extends BaseModel
{
    public function company(): array
    {
        $stmt = $this->db->query(
            "SELECT key_name, value_text
             FROM settings
             WHERE key_name IN ('company_name','company_document','company_email','company_phone','company_address','company_logo')"
        );

        $data = [
            'name' => app_config('company.name'),
            'document' => app_config('company.document'),
            'email' => app_config('company.email'),
            'phone' => app_config('company.phone'),
            'address' => app_config('company.address'),
            'logo' => app_config('company.logo'),
        ];

        foreach ($stmt->fetchAll() as $row) {
            switch ($row['key_name']) {
                case 'company_name':
                    $data['name'] = $row['value_text'];
                    break;
                case 'company_document':
                    $data['document'] = $row['value_text'];
                    break;
                case 'company_email':
                    $data['email'] = $row['value_text'];
                    break;
                case 'company_phone':
                    $data['phone'] = $row['value_text'];
                    break;
                case 'company_address':
                    $data['address'] = $row['value_text'];
                    break;
                case 'company_logo':
                    $data['logo'] = $row['value_text'];
                    break;
            }
        }

        return $data;
    }

    public function saveCompany(array $data): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO settings (key_name, value_text)
             VALUES (:key_name, :value_text)
             ON DUPLICATE KEY UPDATE value_text = VALUES(value_text), updated_at = CURRENT_TIMESTAMP'
        );

        $map = [
            'company_name' => $data['name'],
            'company_document' => $data['document'],
            'company_email' => $data['email'],
            'company_phone' => $data['phone'],
            'company_address' => $data['address'],
            'company_logo' => $data['logo'],
        ];

        foreach ($map as $key => $value) {
            $stmt->execute([
                'key_name' => $key,
                'value_text' => $value,
            ]);
        }
    }

    public function resetApplicationData(): void
    {
        $currentCompany = $this->company();

        $this->db->beginTransaction();

        try {
            $this->db->exec('DELETE FROM quote_items');
            $this->db->exec('DELETE FROM quotes');
            $this->db->exec('DELETE FROM clients');
            $this->db->exec('DELETE FROM products');
            $this->db->exec('DELETE FROM settings');

            $this->db->commit();
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $exception;
        }

        $this->removeGeneratedPdfs();
        $this->removeUploadedCompanyLogo($currentCompany['logo'] ?? '');
    }

    private function removeGeneratedPdfs(): void
    {
        $pdfDirectory = public_path(app_config('app.public_pdf_path', 'pdf'));

        if (!is_dir($pdfDirectory)) {
            return;
        }

        foreach (glob($pdfDirectory . '/*.pdf') ?: [] as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }

    private function removeUploadedCompanyLogo(string $logoPath): void
    {
        $normalizedPath = ltrim(str_replace('\\', '/', $logoPath), '/');

        if ($normalizedPath === '' || strpos($normalizedPath, 'uploads/company/') !== 0) {
            return;
        }

        $fullPath = public_path($normalizedPath);

        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}
