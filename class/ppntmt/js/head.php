<link rel="stylesheet" href="<?php echo $path_to_class_dir; ?>ppntmt/css/normalize.css" />
<!-- light dark mode. Light is default -->
<script>
  // If `prefers-color-scheme` is not supported, fall back to light mode.
  // In this case, light.css will be downloaded with `highest` priority.
  if (window.matchMedia("(prefers-color-scheme)").media === "not all") {
    document.documentElement.style.display = "none";
    document.head.insertAdjacentHTML(
      'beforeend',
      '<link rel="stylesheet" href="<?php echo $path_to_class_dir; ?>ppntmt/css/appointment_light.css" onload="document.documentElement.style.display = \'\'">',
    );
  }
</script>
<link rel="stylesheet" href="<?php echo $path_to_class_dir; ?>ppntmt/css/appointment_dark.css" media="(prefers-color-scheme: dark)">
<link rel="stylesheet" href="<?php echo $path_to_class_dir; ?>ppntmt/css/appointment_light.css" media="(prefers-color-scheme: no-preference), (prefers-color-scheme: light)">
<link rel="stylesheet" href="<?php echo $path_to_class_dir; ?>ppntmt/css/appointment_style.css">
<script type="text/javascript" src="<?php echo $path_to_class_dir; ?>ppntmt/js/appointment.js"></script>
