<?php

namespace App\Helpers;

class NavHelper
{
   /**
    * Check if route is active
    */
   public static function isRouteActive($routes)
   {
      if (is_string($routes)) {
         $routes = [$routes];
      }

      foreach ($routes as $route) {
         if (request()->routeIs($route)) {
            return true;
         }
      }

      return false;
   }

   /**
    * Get active class for nav-link
    */
   public static function activeLink($routes, $class = 'active')
   {
      return self::isRouteActive($routes) ? $class : '';
   }

   /**
    * Get menu-open class for nav-item dropdown
    */
   public static function menuOpen($routes, $class = 'menu-open')
   {
      return self::isRouteActive($routes) ? $class : '';
   }
}
