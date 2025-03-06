<?php
include 'db.php';

// Ajouter un employé
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $position = $_POST['position'];
        $stmt = $pdo->prepare("INSERT INTO employees (name, email, position) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $position]);
    } 
    // Modifier un employé
    elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $position = $_POST['position'];
        $stmt = $pdo->prepare("UPDATE employees SET name = ?, email = ?, position = ? WHERE id = ?");
        $stmt->execute([$name, $email, $position, $id]);
    } 
}

// Supprimer un employé
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM employees WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: employees.php"); // Rediriger pour éviter la suppression multiple lors du rafraîchissement
}

// Récupérer les employés
$stmt = $pdo->query("SELECT * FROM employees");
$employees = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Gestion des Employés</title>
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Gestion des Employés</h1>
    
    <!-- Formulaire pour ajouter/modifier un employé -->
    <form method="POST" class="mb-4">
        <input type="hidden" id="emp_id" name="id">
        <div class="form-row">
            <div class="form-group col-md-3">
                <input type="text" id="emp_name" name="name" class="form-control" placeholder="Nom" required>
            </div>
            <div class="form-group col-md-3">
                <input type="email" id="emp_email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="form-group col-md-3">
                <input type="text" id="emp_position" name="position" class="form-control" placeholder="Poste" required>
            </div>
            <div class="form-group col-md-3">
                <button type="submit" name="add" id="add_btn" class="btn btn-primary">Ajouter</button>
                <button type="submit" name="update" id="update_btn" class="btn btn-success d-none">Mettre à jour</button>
            </div>
        </div>
    </form>

    <!-- Tableau des employés -->
    <table class="table table-bordered">
        <thead class="thead-light">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Poste</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($employees as $employee): ?>
                <tr>
                    <td><?= $employee['id'] ?></td>
                    <td><?= $employee['name'] ?></td>
                    <td><?= $employee['email'] ?></td>
                    <td><?= $employee['position'] ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm edit-btn" 
                                data-id="<?= $employee['id'] ?>" 
                                data-name="<?= $employee['name'] ?>" 
                                data-email="<?= $employee['email'] ?>" 
                                data-position="<?= $employee['position'] ?>">Modifier</button>
                        <a href="?delete=<?= $employee['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cet employé ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    // Script pour remplir le formulaire en mode "Modifier"
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('emp_id').value = this.dataset.id;
            document.getElementById('emp_name').value = this.dataset.name;
            document.getElementById('emp_email').value = this.dataset.email;
            document.getElementById('emp_position').value = this.dataset.position;

            document.getElementById('add_btn').classList.add('d-none'); // Cacher le bouton Ajouter
            document.getElementById('update_btn').classList.remove('d-none'); // Montrer le bouton Modifier
        });
    });
</script>

</body>
</html>
