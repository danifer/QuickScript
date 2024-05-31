<html>
  <body>
    <p>
        <?php
          $host = 'HOST';

          switch (true) {
              case isset($_SERVER['HTTP_HOST']):
                  $host = $_SERVER['HTTP_HOST'];
                  break;
              case isset($_SERVER['SERVER_NAME']):
                  $host = $_SERVER['SERVER_NAME'];
                  break;
          }

          echo implode(' - ', array_filter([
            $host,
            file_get_contents('https://api.ipify.org'),
            date('c')
          ]));
        ?>
    </p>
  </body>
</html>

