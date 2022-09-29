use u373102469_maguv;

DROP TABLE IF EXISTS tictactoe_game;
DROP TABLE IF EXISTS tictactoe_account;

CREATE TABLE `tictactoe_account` (
  `id` varchar(36) NOT NULL primary key,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(50),
  `wins` int(7) NOT NULL DEFAULT 0,
  `losses` int(7) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tictactoe_game` (
  `id` int(12) NOT NULL primary key auto_increment,
  `id_owner` int(12) NOT NULL,
  `id_guest` int(12),
  `id_winner` int(12),
  `first_position` ENUM('X', 'O'),
  `second_position` ENUM('X', 'O'),
  `third_position` ENUM('X', 'O'),
  `fourth_position` ENUM('X', 'O'),
  `fifth_position` ENUM('X', 'O'),
  `sixth_position` ENUM('X', 'O'),
  `seventh_position` ENUM('X', 'O'),
  `eighth_position` ENUM('X', 'O'),
  `nineth_position` ENUM('X', 'O'),
  `turn` ENUM('OWNER', 'GUEST') NOT NULL,
  `status` ENUM('CREATED', 'STARTED', 'DONE') NOT NULL DEFAULT 'CREATED',
  `created_at` datetime NOT NULL,
  `updated_at` datetime,
  FOREIGN KEY (`id_owner`) REFERENCES `tictactoe_account` (`id`),
  FOREIGN KEY (`id_guest`) REFERENCES `tictactoe_account` (`id`),
  FOREIGN KEY (`id_winner`) REFERENCES `tictactoe_account` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
