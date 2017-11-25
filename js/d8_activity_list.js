jQuery(function() {

  function block_flip(region, block_id) {
    jQuery(region + " .block-weather-block" + block_id).flip({
      trigger: "click",
      reverse: true,
      speed: '200',
      front: jQuery(block_id + ' .weather-block .front-block'),
      back: jQuery(block_id + ' .weather-block .back-block'),
      autoSize: true
    });

  }
  block_flip('.region-sidebar-first', '#block-weatherblock');
  block_flip('.region-content', '#block-weatherblock-2');
});
