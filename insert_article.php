<?php
// Vérification si les données du formulaire sont soumises
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connexion à la base de données (à personnaliser avec vos informations)
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "blog-mdev";

    // Création de la connexion
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérification de la connexion
    if ($conn->connect_error) {
        die("La connexion a échoué : " . $conn->connect_error);
    }

    // Récupération des données du formulaire
    $title = $_POST['title'];
    $content = $_POST['content'];
    $image_url = $_POST['image_url'];
    $created_at = $_POST['created_at']; // Date de création de l'article
    $category_id = $_POST['category']; // ID de la catégorie sélectionnée

    // Validation minimale de la date (optionnel)
    if (!isValidDate($created_at)) {
        die('<div class="alert alert-danger mt-3" role="alert">Format de date invalide. Utilisez YYYY-MM-DD.</div>');
    }

    // Préparation des données pour insertion sécurisée
    $title = $conn->real_escape_string($title);
    $content = $conn->real_escape_string($content);
    $image_url = $conn->real_escape_string($image_url);
    $created_at = $conn->real_escape_string($created_at);
    $category_id = (int)$category_id; // Assurez-vous que l'ID de catégorie est un entier

    // Préparation de la requête SQL pour insérer l'article dans la table des articles
    $sql_article = "INSERT INTO articles (title, content, image_url, created_at)
                    VALUES ('$title', '$content', '$image_url', '$created_at')";

    // Exécution de la requête SQL pour insérer l'article
    if ($conn->query($sql_article) === TRUE) {
        // Récupération de l'ID de l'article inséré
        $article_id = $conn->insert_id;

        // Préparation de la requête SQL pour lier l'article à sa catégorie
        $sql_article_category = "INSERT INTO article_categories (article_id, category_id)
                                 VALUES ($article_id, $category_id)";

        // Exécution de la requête SQL pour lier l'article à sa catégorie
        if ($conn->query($sql_article_category) === TRUE) {
            echo '<div class="alert alert-success mt-3" role="alert">L\'article a été ajouté avec succès.</div>';
        } else {
            echo '<div class="alert alert-danger mt-3" role="alert">Erreur lors de l\'ajout de l\'article : ' . $conn->error . '</div>';
        }
    } else {
        echo '<div class="alert alert-danger mt-3" role="alert">Erreur lors de l\'ajout de l\'article : ' . $conn->error . '</div>';
    }

    // Fermeture de la connexion
    $conn->close();
}

// Fonction utilitaire pour valider le format de date (YYYY-MM-DD)
function isValidDate($date) {
    $format = 'Y-m-d';
    $dateObj = DateTime::createFromFormat($format, $date);
    return $dateObj && $dateObj->format($format) === $date;
}
?>
