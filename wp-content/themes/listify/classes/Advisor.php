<?php

class Advisor extends User
{

    public function test()
    {
      $current_user = wp_get_current_user();

      var_dump($current_user);
    }
}
