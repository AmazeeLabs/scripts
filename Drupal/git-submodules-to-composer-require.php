<?php

// Git submodules to composer-require commands.
// Run with
// $ drush scr path_to_this_script.php

$repositories = '';
$installer_paths = '';
$require = 'composer require';

$output = shell_exec('git submodule foreach');
foreach (array_filter(array_map('trim', explode("\n", $output))) as $string) {

  $submodule_path = preg_replace("/.*'([^']+)'$/", "$1", $string);

  $project_name = preg_replace('~.*/~', '', $submodule_path);

  chdir(DRUPAL_ROOT . '/' . $submodule_path);

  $tag = trim(shell_exec('git describe --exact-match --tags 2>/dev/null'));

  if ($tag) {
    $version = preg_replace('/^8\.x-/', '', $tag);
    $require .= " drupal/$project_name:$version";
  }
  else {
    $branch = trim(shell_exec('git branch -a --contains HEAD 2>/dev/null | grep "remotes/origin/" | head -n1 | sed -e "s/remotes\/origin\///"'));
    $hash = trim(shell_exec('git rev-parse HEAD'));
    $url = trim(shell_exec('git remote get-url origin'));
    if (strpos($url, 'drupal.org') === FALSE) {
      $repositories .= "        {
            \"type\":\"package\",
            \"package\": {
                \"name\": \"drupal/$project_name\",
                \"version\": \"dev-amazee\",
                \"type\": \"drupal-module\",
                \"source\": {
                    \"type\": \"git\",
                    \"url\": \"$url\",
                    \"reference\": \"$branch\"
                }
            }
        },\n";
      if (strpos($submodule_path, '/custom/') !== FALSE) {
        $installer_paths .= "            \"web/modules/custom/$project_name\": [\"drupal/$project_name\"],\n";
      }
      $require .= " drupal/$project_name:dev-amazee#$hash";
    }
    else {
      $version = preg_replace('/^8\.x-/', '', $branch) . '-dev';
      $require .= " drupal/$project_name:$version#$hash";
    }
  }
}

print "\n\nAdd to beginning of 'repositories':\n\n$repositories\n\n";
print "\n\nAdd to beginning of 'installer-paths':\n\n$installer_paths\n\n";
print "\n\nRun:\n\n$require\n\n";
