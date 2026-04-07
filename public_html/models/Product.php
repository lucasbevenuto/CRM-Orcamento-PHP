<?php

class Product extends BaseModel
{
    public function all(string $search = ''): array
    {
        $sql = 'SELECT * FROM products';
        $params = [];

        if ($search !== '') {
            $sql .= ' WHERE name LIKE :search';
            $params['search'] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY name ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch();

        return $product ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO products (name, description, unit_price, cost_price, created_at, updated_at)
             VALUES (:name, :description, :unit_price, :cost_price, NOW(), NOW())'
        );

        $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'],
            'unit_price' => $data['unit_price'],
            'cost_price' => $data['cost_price'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE products
             SET name = :name, description = :description, unit_price = :unit_price, cost_price = :cost_price, updated_at = NOW()
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'description' => $data['description'],
            'unit_price' => $data['unit_price'],
            'cost_price' => $data['cost_price'],
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM products WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
