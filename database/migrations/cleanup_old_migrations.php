<?php

/**
 * Script pour nettoyer les anciennes migrations et conserver uniquement les nouvelles
 * 
 * UTILISATION :
 * 1. Sauvegardez votre base de données
 * 2. Exécutez ce script depuis la racine : php database/migrations/cleanup_old_migrations.php
 * 3. Vérifiez que les anciennes migrations ont été supprimées
 * 4. Exécutez php artisan migrate:fresh --seed
 * 5. Supprimez ce script après utilisation
 */

// Liste des anciennes migrations à supprimer
$oldMigrations = [
    '0001_01_01_000001_create_cache_table.php',
    '0001_01_01_000002_create_jobs_table.php',
    '2024_04_17_000001_create_activities_table.php',
    '2024_04_17_000003_create_documents_table.php',
    '2024_04_17_000004_create_evaluations_table.php',
    '2024_04_17_000005_add_double_encadrant_to_users_table.php',
    '2026_03_22_134224_add_encadrant_id_to_users_table.php',
    '2026_03_24_144139_add_photo_to_users_table.php',
    '2026_03_24_163934_create_password_reset_tokens_table.php',
    '2026_03_31_144309_add_encadrant_id_to_users_table.php',
    '2026_04_02_191251_create_offre_stages_table.php',
    '2026_04_02_191258_create_candidatures_table.php',
    '2026_04_02_191311_create_entreprises_table.php',
    '2026_04_02_191320_create_notifications_table.php',
    '2026_04_02_194424_add_secteur_to_offre_stages_table.php',
    '2026_04_02_213055_add_actif_to_entreprises_table.php',
    '2026_04_02_213713_add_missing_columns_to_entreprises_table.php',
    '2026_04_02_214018_add_type_to_offre_stages_table.php',
    '2026_04_02_220817_add_type_stage_to_offre_stages_table.php',
    '2026_04_02_225227_add_message_to_candidatures_table.php',
    '2026_04_02_225516_make_date_naissance_nullable_in_candidatures_table.php',
    '2026_04_02_225627_make_extra_fields_nullable_in_candidatures_table.php',
    '2026_04_02_230840_add_decision_fields_to_candidatures_table.php',
    '2026_04_10_142457_add_archived_at_to_candidatures_table.php',
    '2026_04_10_153000_add_archived_at_to_candidatures_table.php',
    '2026_04_10_164645_add_reglement_interne_to_entreprises_table.php',
    '2026_04_10_171659_add_photo_path_to_users_table.php',
    '2026_04_10_174259_add_lettre_motivation_to_candidatures_table.php',
    '2026_04_10_175033_make_lettre_motivation_path_nullable_in_candidatures_table.php',
    '2026_04_10_175313_increase_statut_column_size_in_candidatures_table.php',
    '2026_04_10_180440_add_date_decision_to_candidatures_table.php',
    '2026_04_10_180602_add_commentaire_to_candidatures_table.php',
    '2026_04_14_151255_make_cv_path_nullable_in_candidatures_table.php',
    '2026_04_17_123601_add_planning_to_users_table.php',
    '2026_04_17_162452_add_offre_stage_id_to_users_table.php',
    '2026_04_17_162850_add_user_id_to_candidatures_table.php',
    '2026_04_17_164513_add_user_id_to_activities_table.php',
    '2026_04_17_171351_create_discussions_table.php',
    // Nouvelles migrations créées qui pourraient être en double
    '2024_01_01_000010_add_roles_and_profile_to_users_table.php',
    '2024_01_01_000011_create_roles_table.php',
    '2024_04_17_000002_create_submissions_table.php',
    '2024_04_17_000006_create_system_notifications_table.php',
    '2024_04_17_000007_create_activity_logs_table.php',
    'reorganize_migrations.php',
    'README_MIGRATIONS_ORDER.md',
    'DATABASE_RELATIONS.md'
];

echo "=== Nettoyage des anciennes migrations ===\n\n";

$migrationsDir = __DIR__;
$deletedCount = 0;
$errorCount = 0;

foreach ($oldMigrations as $migration) {
    $filePath = $migrationsDir . DIRECTORY_SEPARATOR . $migration;
    
    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            echo "Supprimé: $migration\n";
            $deletedCount++;
        } else {
            echo "ERREUR: Impossible de supprimer $migration\n";
            $errorCount++;
        }
    } else {
        echo "INFO: $migration n'existe pas\n";
    }
}

echo "\n=== Résumé ===\n";
echo "Fichiers supprimés: $deletedCount\n";
echo "Erreurs: $errorCount\n";

if ($errorCount === 0) {
    echo "\nNettoyage terminé avec succès!\n";
    echo "\nProchaines étapes :\n";
    echo "1. php artisan migrate:fresh --seed\n";
    echo "2. Vérifiez que toutes les tables sont créées correctement\n";
    echo "3. Supprimez ce script\n";
} else {
    echo "\nDes erreurs sont survenues. Veuillez vérifier manuellement.\n";
}

// Afficher les migrations restantes
echo "\n=== Migrations restantes ===\n";
$files = scandir($migrationsDir);
foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        echo "- $file\n";
    }
}
