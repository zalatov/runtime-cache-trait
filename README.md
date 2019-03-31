## RuntimeCacheTrait

1. Не требуется указание лишнего private/protected атрибута.
2. Не дублируется аннотация/документация и к атрибуту, и к методу.
3. Инкапсулирование логики методом - есть уверенность, что в других местах атрибут ничего не изменит.

#### Обычное использование

```php
<?php
class Product {
    private $images;

    public function getImages(): array {
        if (null === $this->images) {
            $this->images = [new Image];
        }

        return $this->images;
    }
}
```

#### Через RuntimeCacheTrait

```php
<?php
class Product {
    use RuntimeCacheTrait;

    public function getImages(): array {
        return $this->objectRuntimeCache(__METHOD__, function() {
            return [new Image];
        });
    }
}
```

#### Один экземпляр объекта/модели

```php
<?php
class Product extends \yii\db\ActiveRecord {
    use RuntimeCacheTrait;

    public static function getModel(string $id): ?self {
        return static::globalRuntimeCache([__METHOD__, $id], function() use ($id) {
            return static::findOne($Id);
        });
    }
}
```
