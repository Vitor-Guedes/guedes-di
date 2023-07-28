# GuedesDI - Dependecy Injection Container
Container para gerenciar as dependencias dos projetos pessoais.

## Forma de uso

### Instânciando o container e guardando um intancia de Connection
```php
    $container = Container::build();

    $container->set('connection', function (Connection $connection) {
        return $connection->getInstance();
    });
```
### Obtendo a instancia guarda
```php
    $connection = $container->get('connection');
```

## Criando Classes Com Depedencias
```php
    $container = Container::build();

    $instance = $container->make(MyNamespace\MyClass::class, ['parameter' => 'value_parameter']);

    $result = $instance->getParamter();
    // $result = value_parameter
```
Classe Singleton
```php
    $instance = $container->makeSingleton(MyNamespace\MyClass::class);
```