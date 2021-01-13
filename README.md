# Json to PHP Object Library

### 功能

+ 实现从json生成对应的php对象
+ 支持对象嵌套
+ 支持object[],object[][] 多层级数组对象结构
+ 提供Map类, 区分array与map

### 性能

(JsonMapper 对象内置分析缓存, 一个进程内实例化一次效果最佳)

+ 简单对象 可生成 150000w+/s
+ 复杂对象 可生成 1w+/s

### 安装

`composer require fwhat/jsonmapper`

### 使用示例

+ 其他使用示例可参考[单元测试](tests/JsonMapperTest.php)

```php
use Fwhat\JsonMapper\JsonMapper;
class SetObject {
    public bool $bool;
    public int $int;
    public ?string $string = null;
    public array $arrayString;
    public array $arrayInt;
    public float $float;

    /**
     * @var array
     */
    public array $arrayWithDoc;

    public function setString (string $str) {
        $this->string = "from_set_".$str;
    }
}

$jsonStr = '{
  "bool": true,
  "int": 1,
  "string": "string",
  "arrayString": [
    "arrayString",
    "arrayString"
  ],
  "arrayInt": [
    "arrayInt",
    "arrayInt"
  ],
  "float": 1.23,
  "arrayWithDoc": ["arrayWithDoc"]
}';


$mapper = new JsonMapper;
$object = new SetObject();
$mapper->map($jsonStr, $object);
```
