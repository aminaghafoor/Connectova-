(function () {
  function updateStatus() {
    fetch("update_status.php", {
      method: "POST",
      credentials: "same-origin",
      cache: "no-store",
    }).catch(function () {
      // Ignore background status errors.
    });
  }

  updateStatus();

  setInterval(updateStatus, 60000);
})();
