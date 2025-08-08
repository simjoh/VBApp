<?php
// Variables available from $emailData via extract():
// $participant, $track, $organizer, $oldStartNumber, $newStartNumber, $competitor, $competitorInfo, $refNr

$name = htmlspecialchars($competitor->getGivenName() . ' ' . $competitor->getFamilyName());
$trackTitle = htmlspecialchars($track->getTitle());
// Handle both string and DateTime for start date
$rawDate = $track->getStartDateTime();
if ($rawDate instanceof \DateTimeInterface) {
  $eventDate = htmlspecialchars($rawDate->format('Y-m-d H:i'));
} else {
  try {
    $dt = new \DateTime((string)$rawDate);
    $eventDate = htmlspecialchars($dt->format('Y-m-d H:i'));
  } catch (\Exception $e) {
    $eventDate = htmlspecialchars((string)$rawDate);
  }
}
$startChanged = ($oldStartNumber !== $newStartNumber);
?>
<div style="font-family: Arial, sans-serif; background: #fafafa; padding: 24px;">
  <div style="max-width: 680px; margin: 0 auto; background: #ffffff; border: 1px solid #eee; border-radius: 6px; overflow: hidden;">
    <div style="padding: 20px 24px;">
      <h2 style="margin: 0 0 12px 0; font-size: 22px;">Ditt deltagande har flyttats</h2>
      <p style="margin: 0;">Hej <?php echo $name; ?>,</p>
      <p style="margin: 12px 0 0 0;">Vi vill informera att ditt deltagande har flyttats till banan <strong><?php echo $trackTitle; ?></strong>.</p>
    </div>

    <?php if ($startChanged): ?>
      <div style="background: #FFF7E6; border-top: 1px solid #FDE8C3; border-bottom: 1px solid #FDE8C3; padding: 16px 24px;">
        <h3 style="margin: 0 0 8px 0; font-size: 16px; color: #8a6100;">Viktig information</h3>
        <p style="margin: 0;">Ditt startnummer har ändrats från <strong><?php echo htmlspecialchars($oldStartNumber); ?></strong> till <strong><?php echo htmlspecialchars($newStartNumber); ?></strong>.</p>
        <p style="margin: 8px 0 0 0;">Detta påverkar dina inloggningsuppgifter för eBrevet.</p>
      </div>
    <?php endif; ?>

    <div style="padding: 16px 24px;">
      <h3 style="margin: 0 0 8px 0; font-size: 16px;">Uppdaterad deltagarinformation</h3>
      <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
        <tr>
          <td style="padding: 6px 0; color: #666;">Bana</td>
          <td style="padding: 6px 0; font-weight: 600;"><?php echo $trackTitle; ?></td>
        </tr>
        <tr>
          <td style="padding: 6px 0; color: #666;">Datum</td>
          <td style="padding: 6px 0; font-weight: 600;"><?php echo $eventDate; ?></td>
        </tr>
        <tr>
          <td style="padding: 6px 0; color: #666;">Startnummer</td>
          <td style="padding: 6px 0; font-weight: 600;"><?php echo htmlspecialchars($newStartNumber); ?></td>
        </tr>
        <tr>
          <td style="padding: 6px 0; color: #666;">Referensnummer</td>
          <td style="padding: 6px 0; font-weight: 600;"><?php echo htmlspecialchars($refNr); ?></td>
        </tr>
      </table>

      <div style="margin-top: 12px;">
        <a href="https://ebrevet.org" style="display: inline-block; background: #0ea5e9; color: #ffffff; text-decoration: none; padding: 10px 14px; border-radius: 4px;">Logga in på eBrevet</a>
      </div>
    </div>

    <div style="border-top: 1px solid #eee; padding: 16px 24px; font-size: 13px; color: #666;">
      <p style="margin: 0;">Arrangör: <?php echo htmlspecialchars($organizer); ?></p>
      <p style="margin: 6px 0 0 0;">Om du har några frågor, vänligen kontakta arrangören.</p>
    </div>
  </div>
</div>


