    <div class="toast-container" id="toast-container"></div>
    <script src="<?php echo BASE_URL; ?>/js/main.js"></script>
    <?php if (isset($extra_js)): ?>
      <?php foreach ($extra_js as $js): ?>
        <script src="<?php echo $js; ?>"></script>
      <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
