</main>

    <?php if(isset($_SESSION['user_id'])): ?>
    <nav class="fixed bottom-0 left-0 right-0 h-20 bg-slate-900 border-t border-slate-800 flex items-center justify-around px-2 z-50 pb-2">
        <?php 
        $current_page = basename($_SERVER['PHP_SELF']); 
        $nav_items = [
            ['index.php', 'fa-house', 'Feed'],
            ['tournaments.php', 'fa-trophy', 'Compete'],
            ['marketplace.php', 'fa-shop', 'Market'],
            ['profile.php', 'fa-id-card', 'Profile']
        ];
        foreach($nav_items as $item):
            $is_active = ($current_page == $item[0]);
            $color_class = $is_active ? 'text-purple-400' : 'text-slate-500 hover:text-slate-400';
        ?>
            <a href="<?= $item[0]; ?>" class="flex flex-col items-center justify-center w-16 h-12 transition-transform active:scale-90">
                <i class="fa-solid <?= $item[1]; ?> text-lg <?= $color_class; ?> mb-1"></i>
                <span class="text-[10px] uppercase tracking-wider font-semibold <?= $is_active ? 'text-slate-200' : 'text-slate-500'; ?>"><?= $item[2]; ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
    <?php endif; ?>

    <script>
        // Terminate Context Menus
        document.addEventListener('contextmenu', e => e.preventDefault());
        
        // Terminate Text Selection Hooks
        document.addEventListener('selectstart', e => e.preventDefault());
        
        // Block Keyboard Zoom Modifiers
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && (e.key === '=' || e.key === '-' || e.key === '0')) {
                e.preventDefault();
            }
        });
        
        // Suppress Viewport Scale Snapping on Touch Devices
        document.addEventListener('touchstart', function(e) {
            if (e.touches.length > 1) {
                e.preventDefault();
            }
        }, { passive: false });
    </script>
</body>
</html>