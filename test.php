<?php
include 'db.php';

// CRUD operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        // Ajouter un client
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $stmt = $pdo->prepare("INSERT INTO clients (name, email, phone) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $phone]);
    } elseif (isset($_POST['delete'])) {
        // Supprimer un client
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM clients WHERE id = ?");
        $stmt->execute([$id]);
    } elseif (isset($_POST['update'])) {
        // Modifier un client
        $id = $_POST['id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $stmt = $pdo->prepare("UPDATE clients SET name = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->execute([$name, $email, $phone, $id]);
    }
    header("Location: " . $_SERVER['PHP_SELF']); // Rediriger pour actualiser la page
    exit();
}

// Récupérer les clients
$stmt = $pdo->query("SELECT * FROM clients");
$clients = $stmt->fetchAll();

// Récupérer les informations du client à modifier
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit_id'];
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
    $stmt->execute([$edit_id]);
    $client_to_edit = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Gestion des Clients</title>
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Gestion des Clients</h1>
    <form method="POST" class="mb-4">
        <div class="form-row">
            <div class="form-group col-md-4">
                <input type="text" name="name" class="form-control" placeholder="Nom" required>
            </div>
            <div class="form-group col-md-4">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="form-group col-md-4">
                <input type="text" name="phone" class="form-control" placeholder="Téléphone" required>
            </div>
        </div>
        <button type="submit" name="add" class="btn btn-primary">Ajouter</button>
    </form>

    <?php if (isset($client_to_edit)): ?>
        <h2 class="mt-5">Modifier un client</h2>
        <form method="POST" class="mb-4">
            <input type="hidden" name="id" value="<?= $client_to_edit['id'] ?>">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <input type="text" name="name" class="form-control" value="<?= $client_to_edit['name'] ?>" required>
                </div>
                <div class="form-group col-md-4">
                    <input type="email" name="email" class="form-control" value="<?= $client_to_edit['email'] ?>" required>
                </div>
                <div class="form-group col-md-4">
                    <input type="text" name="phone" class="form-control" value="<?= $client_to_edit['phone'] ?>" required>
                </div>
            </div>
            <button type="submit" name="update" class="btn btn-success">Mettre à jour</button>
        </form>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead class="thead-light">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clients as $client): ?>
                <tr>
                    <td><?= $client['id'] ?></td>
                    <td><?= $client['name'] ?></td>
                    <td><?= $client['email'] ?></td>
                    <td><?= $client['phone'] ?></td>
                    <td>
                        <form method="GET" style="display:inline;">
                            <input type="hidden" name="edit_id" value="<?= $client['id'] ?>">
                            <button type="submit" name="edit" class="btn btn-warning btn-sm">Modifier</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $client['id'] ?>">
                            <button type="submit" name="delete" class="btn btn-danger btn-sm">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>