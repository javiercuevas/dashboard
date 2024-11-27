<?php

    // clear flash
    $_SESSION['flash_type'] = null;
    $_SESSION['flash_message'] = '';
    
    // timer
    $msc = microtime(true)-$msc;
    echo '<br /> <span class="light-gray-sm">execution time: ' . number_format($msc * 1000.00 / 1000.00, 2) . ' secs</span>'; // in seconds
    echo '<br /><br />';
?>

        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /#page-wrapper -->
</div>
<!-- /#wrapper -->

<br /><br />


</body>

</html>


