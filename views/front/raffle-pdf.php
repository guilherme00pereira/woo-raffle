<?php
$rows = $args['rows'] ?? [];
?>
<html lang="utf-8">
<body>
    <table>
        <thead>
            <tr>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr style="background-color: #333; color: #FFF">
                <td>PARTICIPANTES</td>
                <td>NÚMERO DA SORTE</td>
            </tr>
            <?php if(count($rows) > 0):
                foreach($rows as $row): ?>
                    <tr>
                        <td><?php echo $row[0] ?? ''; ?></td>
                        <td><?php echo $row[1] ?? ''; ?></td>
                    </tr>
                <?php endforeach;
            endif; ?>
        </tbody>
    </table>
</body>
</html>