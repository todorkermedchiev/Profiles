<?php
// views/profiles/list.php
?>
<div class="col-md-2">
    <?php include_once 'views/partials/menu.php'?>
</div>
<div class="col-md-10">
    <?php if (!empty($profiles)): ?>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Names</th>
                <th scope="col">Email</th>
                <th scope="col">Phone</th>
                <th scope="col">Birthday</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($profiles as $index => $profile): ?>
            <tr>
                <th scope="row"><?= ($index + 1) + ($itemsPerPage * ($currentPage - 1)); ?></th>
                <td>
                    <a href="<?= create_url('profiles', 'single', ['id' => $profile['id']]); ?>">
                        <?= $profile['first_name'] . ' ' . $profile['last_name']; ?>
                    </a>
                </td>
                <td><?= $profile['email']; ?></td>
                <td><?= $profile['phone']; ?></td>
                <td><?= $profile['birthday']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <?php if ($currentPage > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="index.php?currentPage=<?= $currentPage - 1; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if ($currentPage > DISPLACEMENT + 1): ?>
                    <li class="<?= $liClass; ?>">
                        <a class="page-link" href="index.php?currentPage=<?= $currentPage - JUMP; ?>">...</a>
                    </li>
                <?php endif; ?>
                    
                <?php for ($i = $currentPage - DISPLACEMENT; $i <= $currentPage + DISPLACEMENT; ++$i): ?>
                    <?php if ($i > 0 && $i <= $totalPages): ?>
                        <?php
                        $liClass = 'page-item';
                        if ($i === $currentPage) {
                            $liClass .= ' active';
                        }
                        ?>
                        <li class="<?= $liClass; ?>">
                            <a class="page-link" href="index.php?currentPage=<?= $i; ?>"><?= $i; ?></a>
                        </li>
                    <?php endif; ?>
                <?php endfor; ?>
                        
                <?php if ($currentPage < $totalPages - DISPLACEMENT): ?>
                    <li class="<?= $liClass; ?>">
                        <a class="page-link" href="index.php?currentPage=<?= $currentPage + JUMP; ?>">...</a>
                    </li>
                <?php endif; ?>
                    
                <?php if ($currentPage < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="index.php?currentPage=<?= $currentPage + 1; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    
    <?php else: ?>
    <div class="alert  alert-warning">No profiles loaded</div>
    <?php endif; ?>
</diw>    