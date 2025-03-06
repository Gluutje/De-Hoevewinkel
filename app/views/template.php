<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>De Hoevewinkel Automaat</title>
    <base href="/">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="machine-container">
        <!-- Debug informatie -->
        <?php if (isset($slots)) {
            echo "<!-- Debug: " . count($slots) . " slots gevonden. Status van slot 1: ";
            $slot1 = array_filter($slots, function($s) { return $s['slot_number'] == 1; });
            $slot1 = reset($slot1);
            echo $slot1 ? $slot1['status'] : 'niet gevonden';
            echo " -->";
        } ?>
        <!-- Fysieke automaat met vakken -->
        <div class="physical-machine">
            <h1 class="machine-section-header">Gekoelde Producten</h1>
            <!-- Gekoelde vakken -->
            <div class="slots-container cooled-section">
                <?php 
                if (isset($slots)) {
                    for($i = 1; $i <= 8; $i++) {
                        $slot = array_filter($slots, function($s) use ($i) {
                            return $s['slot_number'] == $i;
                        });
                        $slot = reset($slot); // Eerste (en enige) element
                        $isFilled = $slot && $slot['status'] === 'FILLED';
                        ?>
                        <div class="slot <?php echo $isFilled ? 'FILLED' : 'EMPTY'; ?>" 
                             data-slot-number="<?php echo $i; ?>">
                            <span class="slot-number"><?php echo $i; ?></span>
                            <div class="slot-content">
                                <?php echo $isFilled ? htmlspecialchars($slot['product_name']) : 'Leeg'; ?>
                            </div>
                            <div class="slot-stock">
                                <?php echo $isFilled ? $slot['product_stock'] . ' beschikbaar' : ''; ?>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            
            <h1 class="machine-section-header">Overige Producten</h1>
            <!-- Ongekoelde vakken -->
            <div class="slots-container">
                <?php 
                if (isset($slots)) {
                    for($i = 9; $i <= 16; $i++) {
                        $slot = array_filter($slots, function($s) use ($i) {
                            return $s['slot_number'] == $i;
                        });
                        $slot = reset($slot); // Eerste (en enige) element
                        $isFilled = $slot && $slot['status'] === 'FILLED';
                        ?>
                        <div class="slot <?php echo $isFilled ? 'FILLED' : 'EMPTY'; ?>" 
                             data-slot-number="<?php echo $i; ?>">
                            <span class="slot-number"><?php echo $i; ?></span>
                            <div class="slot-content">
                                <?php echo $isFilled ? htmlspecialchars($slot['product_name']) : 'Leeg'; ?>
                            </div>
                            <div class="slot-stock">
                                <?php echo $isFilled ? $slot['product_stock'] . ' beschikbaar' : ''; ?>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>

        <!-- Touchscreen interface -->
        <div class="vending-machine">
            <div class="vending-screen">
                <!-- Status balk bovenaan -->
                <div class="status-bar">
                    <div class="date-time" id="datetime"></div>
                    <div class="machine-status">Automaat gereed</div>
                </div>

                <!-- Hoofdcontent gebied -->
                <main class="screen-content">
                    <?php 
                    $viewFile = __DIR__ . "/{$name}.php";
                    if (file_exists($viewFile)) {
                        require $viewFile;
                    } else {
                        echo "View niet gevonden: {$name}";
                    }
                    ?>
                </main>

                <!-- Navigatie/actie knoppen onderaan -->
                <div class="action-bar">
                    <button class="action-button" id="btnHome">
                        <span class="icon">🏠</span>
                        <span class="label">Home</span>
                    </button>
                    <button class="action-button" id="btnBack">
                        <span class="icon">⬅️</span>
                        <span class="label">Terug</span>
                    </button>
                    <button class="action-button" id="btnAdmin">
                        <span class="icon">🔑</span>
                        <span class="label">Admin</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Basis JavaScript -->
    <script src="public/js/main.js"></script>
</body>
</html> 