<div class="tableWrap">
  <table class="profile">
    <tbody>
      <tr>
        <th class="row2" style="width: 20%;">Display Name</th>
        <td class="row1" style="width: 80%;">
          <?php echo $user['displayname']; ?>
        </td>
      </tr>
      <tr>
        <th class="row2" style="width: 20%;">Username</th>
        <td class="row1" style="width: 80%;">
          <?php echo $user['username']; ?>
        </td>
      </tr>
      <tr>
        <th class="row2" style="width: 20%;">Email</th>
        <td class="row1" style="width: 80%;">
          <a href="mailto:<?php echo $user['email']; ?>"><?php echo $user['email']; ?></a>
        </td>
      </tr>
      <tr>
        <th class="row2" style="width: 20%;">Avatar</th>
        <td class="row1" style="width: 80%;">
          <?php echo $user['avatar-display']; ?>
        </td>
      </tr>
      <tr>
        <th class="row2" style="width: 20%;">Signature</th>
        <td class="row1" style="width: 80%;">
          <?php echo $user['signature']; ?>
        </td>
      </tr>
    </tbody>
  </table>
</div>