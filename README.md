# Sombatek - Marketplace E-commerce

## Description

Sombatek est une plateforme de marketplace e-commerce moderne construite avec Laravel et Vue.js, offrant une expérience d'achat et de vente fluide et intuitive.

## Technologies Utilisées

- **Backend**: Laravel 12  
- **Frontend**: Inertia + Vue.js 3 + TypeScript  
- **Base de données**: MySQL  
- **Authentification**: Laravel Sanctum  
- **État**: Pinia  
- **UI**: Tailwind CSS  
- **Animations**: Vue Transition  

## Fonctionnalités Principales

- 🛍️ Gestion des produits et des boutiques  
- 👤 Système d'authentification complet  
- 🛒 Panier d'achat dynamique  
- ❤️ Liste de souhaits  
- ⭐ Système de notation et avis  
- 🔍 Recherche avancée  
- 📱 Design responsive  
- 🌙 Mode sombre  

## Prérequis

- PHP 8.1+  
- Node.js 16+  
- Composer  
- MySQL 8.0+  

## Installation

1. Cloner le repository

```bash
git clone https://github.com/auredulvresse/sombatek.git
cd sombatek
````

2. Installer les dépendances PHP

```bash
composer install
```

3. Installer les dépendances JavaScript

```bash
npm install
```

4. Configurer l'environnement

```bash
cp .env.example .env
php artisan key:generate
```

5. Configurer la base de données dans `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sombatek
DB_USERNAME=root
DB_PASSWORD=
```

6. Exécuter les migrations

```bash
php artisan migrate
```

7. Lancer le serveur de développement

```bash
# Terminal 1 - Backend
php artisan serve

# Terminal 2 - Frontend
npm run dev
```

## Structure du Projet

```
sombatek/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   └── Models/
├── resources/
│   ├── js/
│   │   ├── components/
│   │   ├── pages/
│   │   └── stores/
│   └── views/
└── routes/
    └── web.php
```

## Licence

Ce projet est une propriété exclusive de SOMBATEK, édité par la société ONDRIS Tech.

Il n'est pas open-source.
Aucune reproduction, distribution ou modification n’est autorisée sans autorisation écrite préalable.
Tous droits réservés © Sombatek.

Consultez le fichier LICENSE pour plus de détails.
