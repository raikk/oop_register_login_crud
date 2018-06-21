<table class="table table-striped">
    <thead>
      <tr>
	 	 <th>Sl No.</th>
		 <th>Task</th>
		 <th>Image</th>
		 <th>Edit  </th>
		 <th>Delete  </th>
      </tr>
    </thead>
    <tbody>
	<?php
			//print_r($rec);
			$no = 1;
			$numItems = count($rec);
			$num2 = $numItems-1;
			$i = 0;
			echo $rec['total'];
			foreach($rec as $r){
				if( ++$i === $num2) {
				   break;
				}
				$no++;
				?>
      <tr>
        <td><?=$no;?></td>
        <td><?php echo $r['task']; ?></td>
        <td><img src="uploads/<?=$r['image'];?>" alt="Smiley face" height="100" width="100"></td>
		<td><a href="home.php?edit=<?=$r['id'];?>">Edit</a></td>
		<td><<a href="home.php?delete=<?=$r['id'];?>">Delete</a>/td>
      </tr>
	
    </tbody>
  </table>