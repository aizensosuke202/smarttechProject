<?php
include 'db.php';

// CRUD operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['upload'])) {
        // Télécharger un document
        $title = $_POST['title'];
        $file_path = 'uploads/' . basename($_FILES['file']['name']);
        move_uploaded_file($_FILES['file']['tmp_name'], $file_path);
        $stmt = $pdo->prepare("INSERT INTO documents (title, file_path) VALUES (?, ?)");
        $stmt->execute([$title, $file_path]);
    } elseif (isset($_POST['delete'])) {
        // Supprimer un document
        $id = $_POST['id'];
        $file_path = $_POST['file_path'];

        // Supprimer le fichier du système de fichiers
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // Supprimer l'entrée de la base de données
        $stmt = $pdo->prepare("DELETE FROM documents WHERE id = ?");
        $stmt->execute([$id]);
    } elseif (isset($_POST['update'])) {
        // Modifier un document
        $id = $_POST['id'];
        $title = $_POST['title'];
        $old_file_path = $_POST['old_file_path'];

        // Gestion du fichier
        if (!empty($_FILES['file']['name'])) {
            $file_path = 'uploads/' . basename($_FILES['file']['name']);
            move_uploaded_file($_FILES['file']['tmp_name'], $file_path);

            // Supprimer l'ancien fichier
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }
        } else {
            $file_path = $old_file_path; // Conserver l'ancien fichier
        }

        // Mettre à jour le document dans la base de données
        $stmt = $pdo->prepare("UPDATE documents SET title = ?, file_path = ? WHERE id = ?");
        $stmt->execute([$title, $file_path, $id]);
    }
    header("Location: " . $_SERVER['PHP_SELF']); // Rediriger pour actualiser la page
    exit();
}

// Récupérer les documents
$stmt = $pdo->query("SELECT * FROM documents");
$documents = $stmt->fetchAll();

// Récupérer les informations du document à modifier
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit_id'];
    $stmt = $pdo->prepare("SELECT * FROM documents WHERE id = ?");
    $stmt->execute([$edit_id]);
    $document_to_edit = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Gestion des Documents</title>
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Gestion des Documents</h1>
    <form method="POST" enctype="multipart/form-data" class="mb-4">
        <div class="form-group">
            <input type="text" name="title" class="form-control" placeholder="Titre du document" required>
        </div>
        <div class="form-group">
            <input type="file" name="file" class="form-control" required>
        </div>
        <button type="submit" name="upload" class="btn btn-primary">Télécharger</button>
    </form>

    <?php if (isset($document_to_edit)): ?>
        <h2 class="mt-5">Modifier un document</h2>
        <form method="POST" enctype="multipart/form-data" class="mb-4">
            <input type="hidden" name="id" value="<?= $document_to_edit['id'] ?>">
            <input type="hidden" name="old_file_path" value="<?= $document_to_edit['file_path'] ?>">
            <div class="form-group">
                <input type="text" name="title" class="form-control" value="<?= $document_to_edit['title'] ?>" required>
            </div>
            <div class="form-group">
                <input type="file" name="file" class="form-control">
                <small class="form-text text-muted">Laissez vide pour conserver le fichier actuel.</small>
            </div>
            <button type="submit" name="update" class="btn btn-success">Mettre à jour</button>
        </form>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead class="thead-light">
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Chemin du fichier</th>
                <th>Date de téléchargement</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($documents as $document): ?>
                <tr>
                    <td><?= $document['id'] ?></td>
                    <td><?= $document['title'] ?></td>
                    <td><a href="<?= $document['file_path'] ?>" target="_blank">Télécharger</a></td>
                    <td><?= $document['uploaded_at'] ?></td>
                    <td>
                        <form method="GET" style="display:inline;">
                            <input type="hidden" name="edit_id" value="<?= $document['id'] ?>">
                            <button type="submit" name="edit" class="btn btn-warning btn-sm">Modifier</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $document['id'] ?>">
                            <input type="hidden" name="file_path" value="<?= $document['file_path'] ?>">
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