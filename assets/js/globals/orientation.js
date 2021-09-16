window.addEventListener("orientationchange", () => {
  const orientation =
    (screen.orientation || {}).type ||
    screen.mozOrientation ||
    screen.msOrientation;

  console.log(
    "New Orientation: " + orientation
  );
});
