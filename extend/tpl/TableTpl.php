<?php
/**
 * @var $tpl
 */
echo "<?php\n";

?>
/**
* Desc 示例 - 模型
* User
* Date <?= date('Y/m/d') ?>

*/

declare (strict_types = 1);

namespace <?= $tpl['nameSpace'] ?>;

use basic\<?= $tpl['inheritModel'] ?>;
use think\model\concern\SoftDelete;
use traits\ModelTrait;

/**
* This is the model class for table "<?= $tpl['tableName'] ?>".
<?php foreach ($tpl['columns'] as $column): ?>
    * @property string $<?php echo $column['Field'] ?><?php echo $column['Comment'] . PHP_EOL ?>
<?php endforeach; ?>
*/
class <?php echo $tpl['modelName'] ?> extends <?= $tpl['inheritModel'] ?>

{
use ModelTrait;

/**
// 软删除
use SoftDelete;
protected $deleteTime = 'status';
protected $defaultSoftDelete = 1;
* */

<?php foreach ($tpl['columns'] as $column): ?>
    <?php if ($column['Key'] == 'PRI' && $column['Extra'] == 'auto_increment') {
        echo 'protected $pk = ' . "'" . $column['Field'] . "';";
        break;
    } ?>
<?php endforeach; ?>

protected $table = '<?= $tpl['tableName'] ?>';

}