# De Hoevewinkel Automaat

Een touchscreen verkoopautomaat applicatie gebouwd volgens strikte MVC-architectuur.

## Project Structuur

```
/app
  /models         # Data models voor producten, vakken en transacties
  /views         # Scherm templates en UI componenten
  /controllers   # Business logic en request handling
/config          # Database en applicatie configuratie
/public          # Public facing bestanden
/tests          # Test suites
```

## Technische Vereisten

- PHP 8.0+
- MySQL 5.7+
- Modern touchscreen-compatible browser
- Minimale schermresolutie: 1024x768

## Setup Instructies

1. Clone de repository
2. Configureer de database in `config/database.php`
3. Importeer de database schema
4. Start de applicatie via XAMPP

## Ontwikkel Richtlijnen

- Strikte MVC-architectuur
- Één-scherm principe
- Feature-voor-feature testing
- Real-time synchronisatie
- ISO 25010 compliant 