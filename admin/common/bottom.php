</main>

    <?php if(isset($_SESSION['admin_id'])): ?>
    <nav class="fixed bottom-0 left-0 right-0 h-20 bg-slate-900 border-t border-slate-800 flex items-center justify-around px-2 z-50 pb-2">
        <?php 
        $current_page = basename($_SERVER['PHP_SELF']); 
        $nav_items = [
            ['index.php', 'fa-chart-pie', 'Overview']
        ];
        foreach($nav_items as $item):
            $is_active = ($current_page == $item[0]);
            $color_class = $is_active ? 'text-red-400' : 'text-slate-500 hover:text-slate-400';
        ?>
            <a href="<?= $item[0]; ?>" class="flex flex-col items-center justify-center w-24 h-12 transition-transform active:scale-90">
                <i class="fa-solid <?= $item[1]; ?> text-lg <?= $color_class; ?> mb-1"></i>
                <span class="text-[10px] uppercase tracking-wider font-semibold <?= $is_active ? 'text-slate-200' : 'text-slate-500'; ?>"><?= $item[2]; ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
    <?php endif; ?>

    <script>
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('selectstart', e => e.preventDefault());
    </script>
</body>
</html>