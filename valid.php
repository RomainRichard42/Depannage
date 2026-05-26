<?php
// On définit les 3 paramètres de base
$to      = "contact@depannage-accessidev.fr";
$subject = "Test simple";
$message = "Corps de l'email !";

// On envoie
$success = mail($to, $subject, $message);

// On logue en rouge dans ton terminal VS Code (pour le debug local)
echo $success ? "Email envoyé !" : "Échec de l'envoi.";
error_log("\033[31mTentative d'envoi mail : " . ($success ? "OK" : "ERREUR") . "\033[0m");
