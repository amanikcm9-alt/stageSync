# Compréhension des Classes - Sprint 1 & Sprint 2

## Sprint 1 - Accès à la plateforme & gestion des utilisateurs

### Epic 1 - Authentification & Sécurité

#### **Modèles (Models)**

**User** 
- **Rôle :** Représente un utilisateur du système (RH, Admin, Stagiaire, Encadrant)
- **Responsabilités :**
  - Stocker les informations personnelles (nom, prénom, email, téléphone)
  - Gérer le mot de passe et l'email vérifié
  - Définir le rôle de l'utilisateur
  - Relations : appartient à un Role, peut avoir un encadrant, peut encadrer des stagiaires

**Role**
- **Rôle :** Définit les types d'utilisateurs du système
- **Responsabilités :**
  - Stocker le nom du rôle (admin, rh, encadrant, stagiaire)
  - Description du rôle
  - Relations : peut avoir plusieurs Users

#### **Contrôleurs (Controllers)**

**AuthController**
- **Rôle :** Gérer l'authentification des utilisateurs
- **Responsabilités :**
  - Afficher le formulaire de connexion
  - Traiter la tentative de connexion
  - Gérer la déconnexion
  - Rediriger selon le rôle de l'utilisateur

**ProfileController**
- **Rôle :** Gérer le profil personnel des utilisateurs
- **Responsabilités :**
  - Afficher le formulaire de modification du profil
  - Mettre à jour les informations personnelles
  - Changer le mot de passe
  - Sécuriser le compte utilisateur

**AdminUserController**
- **Rôle :** Permettre à l'admin d'attribuer des rôles
- **Responsabilités :**
  - Lister tous les utilisateurs
  - Afficher le formulaire d'attribution de rôle
  - Mettre à jour le rôle d'un utilisateur

#### **Vues (Views)**

**auth/login.blade.php**
- **Rôle :** Page de connexion pour tous les utilisateurs
- **Fonctionnalités :** Formulaire email + mot de passe

**profile/edit.blade.php**
- **Rôle :** Page de modification du profil personnel
- **Fonctionnalités :** Formulaire nom, prénom, email, téléphone + changement mot de passe

**admin/users/index.blade.php**
- **Rôle :** Liste des utilisateurs pour l'admin
- **Fonctionnalités :** Tableau avec sélection de rôle pour chaque utilisateur

---

### Epic 6 - Création & Gestion des Utilisateurs

#### **Contrôleurs (Controllers)**

**RHUserController**
- **Rôle :** Gérer les utilisateurs (stagiaires et encadrants) pour le RH
- **Responsabilités :**
  - Créer des utilisateurs (stagiaires/encadrants)
  - Lister les utilisateurs avec filtres et tri
  - Modifier les informations des utilisateurs
  - Afficher les détails d'un utilisateur

**AdminAssignmentController**
- **Rôle :** Gérer les affectations encadrant-stagiaire
- **Responsabilités :**
  - Afficher la liste des stagiaires et leurs encadrants
  - Affecter un encadrant à un stagiaire
  - Mettre à jour les affectations

**AdminSettingsController**
- **Rôle :** Gérer les paramètres de la plateforme
- **Responsabilités :**
  - Afficher le formulaire des paramètres
  - Mettre à jour les configurations (sécurité, emails, etc.)

#### **Vues (Views)**

**rh/users/create.blade.php**
- **Rôle :** Formulaire de création d'utilisateur pour le RH
- **Fonctionnalités :** Formulaire complet avec sélection du rôle (stagiaire/encadrant)

**rh/users/index.blade.php**
- **Rôle :** Liste des utilisateurs gérés par le RH
- **Fonctionnalités :** Tableau avec filtres, recherche, pagination

**rh/users/edit.blade.php**
- **Rôle :** Formulaire de modification d'utilisateur
- **Fonctionnalités :** Formulaire pré-rempli avec les informations actuelles

**rh/assignments/index.blade.php**
- **Rôle :** Interface d'affectation des encadrants
- **Fonctionnalités :** Tableau des stagiaires avec modal d'affectation

**admin/settings/index.blade.php**
- **Rôle :** Page de configuration de la plateforme
- **Fonctionnalités :** Formulaires pour tous les paramètres système

#### **Modèles (Models)**

**Setting**
- **Rôle :** Stocker les paramètres de configuration de la plateforme
- **Responsabilités :**
  - Sauvegarder les configurations système
  - Récupérer les paramètres
  - Mettre à jour les valeurs

---

## Sprint 2 - Publication des offres & gestion des candidatures

### Epic 2 - Offres & Candidatures

#### **Modèles (Models)**

**OffreStage**
- **Rôle :** Représenter une offre de stage publiée
- **Responsabilités :**
  - Stocker les détails de l'offre (titre, description, dates, rémunération)
  - Gérer le statut (brouillon, publiée, clôturée)
  - Relations : appartient à une Entreprise et un TypeStage, a plusieurs Candidatures

**Entreprise**
- **Rôle :** Représenter une entreprise proposant des stages
- **Responsabilités :**
  - Stocker les informations de l'entreprise (nom, adresse, contact)
  - Relations : peut avoir plusieurs OffreStage

**TypeStage**
- **Rôle :** Catégoriser les types de stages
- **Responsabilités :**
  - Définir les catégories de stages (développement, marketing, etc.)
  - Relations : peut avoir plusieurs OffreStage

**Candidature**
- **Rôle :** Représenter une candidature à une offre
- **Responsabilités :**
  - Stocker les informations du candidat
  - Gérer les documents (CV, lettre de motivation)
  - Suivre le statut (reçue, en cours, acceptée, refusée)
  - Relations : appartient à une OffreStage et un User

**Notification**
- **Rôle :** Gérer les notifications pour les utilisateurs
- **Responsabilités :**
  - Stocker les messages de notification
  - Gérer l'état lu/non lu
  - Relations : appartient à un User

**Entretien**
- **Rôle :** Planifier les entretiens pour les candidatures
- **Responsabilités :**
  - Stocker les détails de l'entretien (date, lieu, type)
  - Gérer le statut de l'entretien
  - Relations : appartient à une Candidature

#### **Contrôleurs (Controllers)**

**RHOffreController**
- **Rôle :** Gérer les offres de stage pour le RH
- **Responsabilités :**
  - Créer de nouvelles offres
  - Modifier les offres existantes
  - Publier/Clôturer les offres
  - Lister les offres avec filtres

**PublicController**
- **Rôle :** Gérer l'accès public aux offres
- **Responsabilités :**
  - Afficher la liste des offres publiées
  - Afficher les détails d'une offre
  - Gérer le formulaire de candidature
  - Traiter les soumissions de candidatures

**RHCandidatureController**
- **Rôle :** Gérer les candidatures pour le RH
- **Responsabilités :**
  - Lister les candidatures avec filtres
  - Afficher les détails d'une candidature
  - Télécharger les documents (CV, lettre)
  - Accepter/refuser les candidatures

**CandidatureController**
- **Rôle :** Traiter les décisions sur les candidatures
- **Responsabilités :**
  - Accepter une candidature (créer compte utilisateur)
  - Refuser une candidature
  - Envoyer les emails de notification

**NotificationController**
- **Rôle :** Gérer les notifications des utilisateurs
- **Responsabilités :**
  - Afficher la liste des notifications
  - Marquer comme lu
  - Marquer tout comme lu

**RHEntretienController**
- **Rôle :** Planifier les entretiens
- **Responsabilités :**
  - Créer un entretien pour une candidature
  - Envoyer les emails de planification
  - Mettre à jour le statut de la candidature

#### **Vues (Views)**

**rh/offres/create.blade.php**
- **Rôle :** Formulaire de création d'offre
- **Fonctionnalités :** Formulaire complet avec tous les détails de l'offre

**rh/offres/index.blade.php**
- **Rôle :** Liste des offres pour le RH
- **Fonctionnalités :** Tableau avec filtres, actions publier/clôturer

**public/offres.blade.php**
- **Rôle :** Page publique des offres
- **Fonctionnalités :** Grille des offres avec recherche et filtres

**public/offre-detail.blade.php**
- **Rôle :** Détails d'une offre spécifique
- **Fonctionnalités :** Informations complètes + bouton de candidature

**public/candidature-form.blade.php**
- **Rôle :** Formulaire de candidature
- **Fonctionnalités :** Formulaire complet avec upload de documents

**rh/candidatures/index.blade.php**
- **Rôle :** Liste des candidatures pour le RH
- **Fonctionnalités :** Tableau avec filtres, téléchargements de documents

**rh/candidatures/show.blade.php**
- **Rôle :** Détails d'une candidature
- **Fonctionnalités :** Informations complètes + actions accepter/refuser

**notifications/index.blade.php**
- **Rôle :** Liste des notifications de l'utilisateur
- **Fonctionnalités :** Liste avec état lu/non lu

**rh/entretiens/create.blade.php**
- **Rôle :** Formulaire de planification d'entretien
- **Fonctionnalités :** Formulaire date/heure/lieu/type

---

## Résumé des Rôles par Type de Classe

### **Modèles (Models) - Couche de Données**
- **User/Role :** Gestion des utilisateurs et permissions
- **OffreStage/Entreprise/TypeStage :** Gestion des offres
- **Candidature :** Gestion des postulations
- **Setting :** Configuration système
- **Notification :** Messages utilisateurs
- **Entretien :** Planification des entretiens

### **Contrôleurs (Controllers) - Couche Logique**
- **AuthController :** Connexion/déconnexion
- **RHUserController :** Gestion utilisateurs RH
- **RHOffreController :** Gestion offres RH
- **RHCandidatureController :** Gestion candidatures RH
- **PublicController :** Accès public aux offres
- **CandidatureController :** Traitement des décisions

### **Vues (Views) - Couche Présentation**
- **auth/* :** Pages d'authentification
- **rh/users/* :** Gestion utilisateurs RH
- **rh/offres/* :** Gestion offres RH
- **rh/candidatures/* :** Gestion candidatures RH
- **public/* :** Pages publiques (offres, candidatures)

Cette architecture sépare clairement les responsabilités : les Modèles gèrent les données, les Contrôleurs la logique métier, et les Vues l'interface utilisateur.
