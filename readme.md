How to install this module:

Step1: First add flowing codes into project `composer.json`

```
"repositories": [
    {
        "type": "gitlab",
        "url": "https://gitlab.com/aminkt/yii2-userAccounting-module"
    }
],
```

Then add flowing line to require part of `composer.json` :
```
"aminkt/yii2-userAccounting-module": "*",
```

And after that run bellow command in your composer :
```
Composer update aminkt/yii2-userAccounting-module
```

Step2: Add flowing lines in your application admin config in module part:

```
'userAccounting' => [
    'class' => \aminkt\userAccounting\UserAccounting::className(),
    'controllerNamespace' => \aminkt\userAccounting\UserAccounting::ADMIN_CONTROLLER_NAMESPACE,
],
```

Step3: Add flowing lines in your application frontend config in module part:

```
'userAccounting' => [
    'class' => \aminkt\userAccounting\UserAccounting::className(),
    'controllerNamespace' => \aminkt\userAccounting\UserAccounting::PANEL_CONTROLLER_NAMESPACE,
],
```
---
**Database Migrations**

Before usage this extension, we'll also need to prepare the database.

```
php yii migrate --migrationPath=@vendor/aminkt/yii2-userAccounting-module/migrations
```

---
Structure of tables and classes:
---
![alt text](structure.png)
