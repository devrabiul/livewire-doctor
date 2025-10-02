# 🩺 LivewireDoctor – Fix Livewire Issues Instantly & Boost Productivity

A powerful Laravel package that **automatically diagnoses, fixes**, and improves your **Livewire** development experience. Save time, eliminate headaches, and keep your components healthy — all with a single command.

[![Latest Stable Version](https://poser.pugx.org/devrabiul/livewire-doctor/v/stable)](https://packagist.org/packages/devrabiul/livewire-doctor)
[![Total Downloads](https://poser.pugx.org/devrabiul/livewire-doctor/downloads)](https://packagist.org/packages/devrabiul/livewire-doctor)
[![Monthly Downloads](https://poser.pugx.org/devrabiul/livewire-doctor/d/monthly)](https://packagist.org/packages/devrabiul/livewire-doctor)
![GitHub license](https://img.shields.io/github/license/devrabiul/livewire-doctor)
[![Buy us a tree](https://img.shields.io/badge/Treeware-%F0%9F%8C%B3-lightgreen)](https://plant.treeware.earth/devrabiul/livewire-doctor)
![GitHub Repo stars](https://img.shields.io/github/stars/devrabiul/livewire-doctor?style=social)

---

## 🚀 Live Demo

👉 [Try the Live Demo](https://packages.rixetbd.com/devrabiul/livewire-doctor)

![Live Demo Thumbnail](https://packages.rixetbd.com/storage/app/public/package/devrabiul/livewire-doctor.webp)

---

## 💡 Overview

**LivewireDoctor** is your personal development assistant for Laravel Livewire apps. It scans your project, detects common issues like missing assets, misconfigured directives, outdated components — and **fixes them automatically**.

Whether you're debugging, onboarding a team, or simply maintaining Livewire-based applications — this package keeps your workflow smooth and efficient.

---

## ✨ Features at a Glance

* ✅ Instantly diagnose common Livewire issues
* 🛠️ Auto-fix configuration and structural problems
* ⚙️ Artisan commands for one-click health checks
* 🔍 Scans for performance tips and improvements
* 🧰 Developer helpers for conditional debugging
* ⚡ Optimized for **Livewire v3**
* 🎯 Compatible with **Laravel 10 & 11**

---

## 📦 Installation

Install via Composer:

```bash
composer require devrabiul/livewire-doctor
```

Optionally publish the config (if required later):

```bash
php artisan vendor:publish --provider="Devrabiul\LivewireDoctor\LivewireDoctorServiceProvider"
```

## ⚙️ Initialize Custom Assets in `AppServiceProvider`

You need to initialize custom Livewire assets `initCustomAsset()` method in your `AppServiceProvider`.

#### ✅ Example:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Devrabiul\LivewireDoctor\LivewireDoctor;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Initializes LivewireDoctor custom asset setup
        LivewireDoctor::initCustomAsset();
    }
}
```

## 🚀 Usage

### 🔎 Run Health Checks

Scan your entire Livewire setup with a single command:

```bash
php artisan livewire:doctor
```

What it checks:

* Livewire installation and version
* Missing assets or directives
* Component structure, naming, and syntax validation

---

## 🛠 Artisan Commands

| Command                             | Description                                    |
| ----------------------------------- | ---------------------------------------------- |
| `php artisan livewire:doctor`       | Run a full health check on your Livewire setup |
---

## ✨ Why Developers Love LivewireDoctor

> ✅ “It just works! Saved me hours of debugging Livewire component issues.”
> ✅ “Every Livewire project should start with `livewire:doctor`.”
> ✅ “Incredible time-saver. One command, and my app is healthy again!”

---

## 🌍 Useful Links

* 🔗 **GitHub:** [Livewire Doctor Repository](https://github.com/devrabiul/livewire-doctor)
* 🔗 **Website:** [https://packages.rixetbd.com/devrabiul/livewire-doctor](https://packages.rixetbd.com/devrabiul/livewire-doctor)
* 🔗 **Packagist:** [https://packagist.org/packages/devrabiul/livewire-doctor](https://packagist.org/packages/devrabiul/livewire-doctor)

---

## 🤝 Contributing

We welcome contributions to LivewireDoctor! If you would like to contribute, please fork the repository and submit a pull request. For any issues or feature requests, please open an issue on GitHub.

---

## 📄 License

Licensed under the [MIT License](LICENSE).

---

## 📬 Contact

For support, bugs, or feature suggestions:

* 📧 Email: [devrabiul@gmail.com](mailto:devrabiul@gmail.com)
* 🌐 GitHub: [@devrabiul](https://github.com/devrabiul)

---
**LivewireDoctor** is the smart way to keep your Laravel Livewire apps running at their best. Install it now — and let your code breathe easy!
