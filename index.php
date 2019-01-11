<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Konversi</title>
    <link rel="stylesheet" href="style/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="style/styles.css">
</head>

<body>
<div class="container" id="form">
    <?php
    if (isset($_POST['sentence'])) {
        include "NumberConverter.php";
        $converter = new NumberConverter();
        $sentence = trim(strtolower($_POST['sentence']));
        $result = $converter->decoder($sentence);
        if (is_numeric($result)) {
            $result = number_format($result);
        }
        ?>
        <div class="heading">
            <h1>Hasil Konversi </h1>
        </div>
        <p class="sentence"><b>Kalimat : </b><?php echo $sentence; ?></p>
        <p class="result"><b>Hasil : </b><?php echo $result; ?></p>
        <div class="clearfix"></div>
        <?php
    } else {
        ?>
        <div class="heading">
            <h1>Konversi Kalimat ke Angka</h1>
        </div>
        <form action="index.php" method="post">
            <div class="form-group">
                <input type="text" class="form-control" name="sentence" placeholder="Masukkan Kalimat">
            </div>
            <div class="form-group form-group-btn">
                <button type="submit" class="btn btn-success btn-lg">Submit</button>
            </div>
            <div class="clearfix"></div>
        </form>
        <?php
    }
    ?>
    <footer class="page-footer font-small blue">

        <!-- Copyright -->
        <div class="footer-copyright text-center py-3">Copyright © 2019
            <a href="https://cakobob.com">Bobby Brillian Yerikho</a>
        </div>
        <div class="footer-copyright text-center py-3">Powered by <a
                    href="https://tutorialzine.com/2016/11/freebie-3-elegant-bootstrap-forms">Freebie: 3 Elegant
                Bootstrap Form</a>
        </div>
        <!-- Copyright -->

    </footer>
</div>
</body>

</html>
