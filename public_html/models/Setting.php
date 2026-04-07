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
}
