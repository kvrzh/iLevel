<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="blocks">
    <div class="find-operations">
        <form action="/" method="POST" id="find">
            <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>"/>
            <input name="date_from" type="date" value="<?= $first ?>">
            <input name="date_to" type="date" value="<?= $last ?>">
            <input type="submit" value="Отправить">
        </form>
    </div>

    <div class="add-operation">
        <form action="/api/operation" id="create">
            <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>"/>
            <input required placeholder="Введите название" type="text" name="title" id="title"/>
            <select required id="type">
                <option value="profit">Profit</option>
                <option value="loss">Loss</option>
            </select>
            <input required type="date" name="date" id="date"/>
            <input required placeholder="Введите сумму" type="number" name="uah" id="uah" step="0.01" min="0">
            <input type="submit" value="Создать">
        </form>
    </div>
</div>
<div class="table">
    <table class="operations table table-hover">
        <thead>
        <tr>
            <th>Title</th>
            <th>Type</th>
            <th>Date</th>
            <th>UAH</th>
            <th>USD</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($operations as $operation): ?>
            <tr class="operation <?= $operation['type'] ?>">
                <td class="title"><?= $operation['title'] ?></td>
                <td class="type"><?= $operation['type'] ?></td>
                <td class="date"><?= $operation['date'] ?></td>
                <td class="uah"><?= $operation['value'] ?></td>
                <td class="usd"></td>
                <td class="actions" id="operation_<?= $operation['id'] ?>">
                    <span data-id="<?= $operation['id'] ?>" class="edit glyphicon glyphicon-pencil"></span>
                    <span data-id="<?= $operation['id'] ?>" class="delete glyphicon glyphicon-trash"></span>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="5"></td>
            <td class="count">Итого: <?= $sum ?></td>
        </tr>
        </tbody>
    </table>
</div>
