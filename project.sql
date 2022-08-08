-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 27. Mai, 2021 14:37 PM
-- Tjener-versjon: 10.4.18-MariaDB
-- PHP Version: 8.0.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `skiapi`
--

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `auth_token`
--

CREATE TABLE `auth_token` (
  `token` varchar(100) NOT NULL,
  `User` enum('Employee','Customer','TransporterEndpoint') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dataark for tabell `auth_token`
--

INSERT INTO `auth_token` (`token`, `User`) VALUES
('asd', 'Employee'),
('qaz', 'Customer'),
('wsx', 'TransporterEndpoint');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `customer`
--

CREATE TABLE `customer` (
  `customerID` int(11) NOT NULL,
  `Cname` varchar(100) NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `shippingAddress` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dataark for tabell `customer`
--

INSERT INTO `customer` (`customerID`, `Cname`, `startDate`, `endDate`, `shippingAddress`) VALUES
(1, 'Ola Nordmann', '2010-01-01', '2030-01-01', 'Storgata 1, 2821 Gjøvik'),
(2, 'Benjamin Beinknuser', '2016-01-01', '2026-12-31', 'Leavnnjageaidnu 1, 9730 Karasjok'),
(3, 'XXL', '2005-01-01', '2025-01-01', 'Strømsveien 245 Alna Senter, 0668 Oslo'),
(4, 'Sport1', '2010-01-01', '2030-01-01', 'Bogstadveien 2, 0355 Oslo'),
(5, 'Intersport', '2010-01-01', '2030-01-01', 'Strømgaten 8, 5015 Bergen'),
(6, 'NRP Sport', '2010-01-01', '2030-01-01', 'Brugata 19, 2380 Brumunddal'),
(7, 'Nissesporten', '1907-01-01', '2907-01-01', 'Nordpolen');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `employee`
--

CREATE TABLE `employee` (
  `employeeNumber` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `department` enum('CustomerRepresentative','ProductionPlanner','Storekeeper') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dataark for tabell `employee`
--

INSERT INTO `employee` (`employeeNumber`, `name`, `department`) VALUES
(1, 'Oline Nordkvinne', 'Storekeeper'),
(2, 'Paul Jeffersson', 'ProductionPlanner'),
(3, 'Ahre-Ketil \"Rambo\" Lillehagen', 'CustomerRepresentative');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `franchise`
--

CREATE TABLE `franchise` (
  `customerID` int(11) NOT NULL,
  `negotiatedPrice` varchar(50) NOT NULL,
  `information` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dataark for tabell `franchise`
--

INSERT INTO `franchise` (`customerID`, `negotiatedPrice`, `information`) VALUES
(3, '0.6', 'Several stores can order independently.'),
(4, '0.6', 'Several stores can order independently.'),
(5, '0.6', 'Several stores can order independently.');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `handlesorder`
--

CREATE TABLE `handlesorder` (
  `employeeNumber` int(11) NOT NULL,
  `orderNumber` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `comment` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dataark for tabell `handlesorder`
--

INSERT INTO `handlesorder` (`employeeNumber`, `orderNumber`, `date`, `comment`) VALUES
(3, 316206, '2021-05-27 11:00:02', 'Updated order to state: open'),
(3, 316206, '2021-05-27 11:01:14', 'Updated order to state: available'),
(3, 316206, '2021-05-27 11:01:41', 'Updated order to state: availabe'),
(3, 316206, '2021-05-27 11:03:11', 'Updated order to state: availabe'),
(3, 316206, '2021-05-27 11:03:55', 'Updated order to state: availabe'),
(3, 316206, '2021-05-27 11:04:02', 'Updated order to state: available'),
(1, 206546, '2021-05-27 11:28:11', 'Assigned ski: 5 to order');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `handlesski`
--

CREATE TABLE `handlesski` (
  `employeeNumber` int(11) NOT NULL,
  `productID` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dataark for tabell `handlesski`
--

INSERT INTO `handlesski` (`employeeNumber`, `productID`, `date`) VALUES
(2, 122221, '2021-05-09 12:48:19'),
(1, 0, '2021-05-27 08:10:58'),
(1, 0, '2021-05-27 08:20:34'),
(1, 19811, '2021-05-27 08:28:52'),
(1, 44649, '2021-05-27 08:28:58');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `ordercontent`
--

CREATE TABLE `ordercontent` (
  `ID` int(6) NOT NULL,
  `orderNumber` int(11) DEFAULT NULL,
  `skiID` int(6) DEFAULT NULL,
  `length` enum('142','147','152','157','162','167','172','177','182','187','192','197','202','207') DEFAULT NULL,
  `weight` enum('20-30','30-40','40-50','50-60','60-70','70-80','80-90','90+') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dataark for tabell `ordercontent`
--

INSERT INTO `ordercontent` (`ID`, `orderNumber`, `skiID`, `length`, `weight`) VALUES
(4985, 316206, 18, '157', '90+'),
(85425, 779075, 1, '157', '90+'),
(85426, 779075, 18, '157', '90+'),
(538716, 206546, 2, '157', '90+');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `orders`
--

CREATE TABLE `orders` (
  `orderNumber` int(11) NOT NULL,
  `quantity` int(4) DEFAULT NULL,
  `totalPrice` float NOT NULL,
  `state` enum('new','open','available','cancelled','ready','shipped') NOT NULL,
  `customerID` int(11) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dataark for tabell `orders`
--

INSERT INTO `orders` (`orderNumber`, `quantity`, `totalPrice`, `state`, `customerID`, `date`) VALUES
(206546, 1, 2500, 'new', 1, '2021-05-27 11:31:35'),
(316206, 1, 7700, 'shipped', 1, '2021-05-27 11:47:07'),
(779075, 2, 10300, 'new', 1, '2021-05-27 12:32:56');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `productionplan`
--

CREATE TABLE `productionplan` (
  `start_date` date NOT NULL,
  `employeeNumber` int(11) NOT NULL,
  `skiID` int(6) DEFAULT NULL,
  `numberOfSki` float NOT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dataark for tabell `productionplan`
--

INSERT INTO `productionplan` (`start_date`, `employeeNumber`, `skiID`, `numberOfSki`, `end_date`) VALUES
('2021-06-06', 2, 1, 1002, '2021-07-06');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `shipment`
--

CREATE TABLE `shipment` (
  `shipmentNumber` int(11) NOT NULL,
  `orderNumber` int(11) NOT NULL,
  `transporterID` int(10) NOT NULL,
  `customerID` int(11) NOT NULL,
  `state` enum('ready','shipped') NOT NULL,
  `scheduledPickUpDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dataark for tabell `shipment`
--

INSERT INTO `shipment` (`shipmentNumber`, `orderNumber`, `transporterID`, `customerID`, `state`, `scheduledPickUpDate`) VALUES
(122424, 874660, 1, 4, 'shipped', '2021-05-09 12:25:06'),
(354553, 316206, 1, 1, 'shipped', '2021-05-27 11:13:56'),
(602202, 458166, 1, 1, 'ready', '2021-05-19 14:29:14'),
(677852, 316206, 1, 1, 'ready', '2021-05-27 11:14:20'),
(815268, 316206, 1, 1, 'ready', '2021-05-27 11:14:35'),
(949509, 316206, 1, 1, 'ready', '2021-05-27 11:15:32');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `shipmentrecordings`
--

CREATE TABLE `shipmentrecordings` (
  `transporterID` int(11) DEFAULT NULL,
  `shipmentNumber` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `comment` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dataark for tabell `shipmentrecordings`
--

INSERT INTO `shipmentrecordings` (`transporterID`, `shipmentNumber`, `date`, `comment`) VALUES
(1, 0, '2021-05-27 11:09:55', 'Created shipment request for order'),
(1, 0, '2021-05-27 11:10:34', 'Created shipment request for order'),
(1, 0, '2021-05-27 11:10:48', 'Created shipment request for order'),
(1, 0, '2021-05-27 11:11:21', 'Created shipment request for order'),
(1, 0, '2021-05-27 11:15:36', 'Created shipment request for order'),
(1, 35453, '2021-05-27 11:51:45', 'Shipped order'),
(2, 122424, '2021-05-09 13:11:48', NULL),
(1, 316206, '2021-05-27 11:11:57', 'Created shipment request for order'),
(1, 316206, '2021-05-27 11:13:00', 'Created shipment request for order'),
(1, 316206, '2021-05-27 11:13:33', 'Created shipment request for order'),
(1, 316206, '2021-05-27 11:13:56', 'Created shipment request for order'),
(1, 316206, '2021-05-27 11:14:20', 'Created shipment request for order'),
(1, 316206, '2021-05-27 11:14:35', 'Created shipment request for order'),
(1, 316206, '2021-05-27 11:15:32', 'Created shipment request for order'),
(1, 316206, '2021-05-27 11:40:59', 'Shipped order'),
(1, 316206, '2021-05-27 11:43:22', 'Shipped order'),
(1, 316206, '2021-05-27 11:43:43', 'Shipped order'),
(1, 316206, '2021-05-27 11:44:24', 'Shipped order'),
(1, 316206, '2021-05-27 11:45:13', 'Shipped order'),
(1, 316206, '2021-05-27 11:45:31', 'Shipped order'),
(1, 316206, '2021-05-27 11:46:28', 'Shipped order'),
(1, 316206, '2021-05-27 11:46:56', 'Shipped order'),
(1, 316206, '2021-05-27 11:47:07', 'Shipped order'),
(1, 354553, '2021-05-27 11:51:12', 'Shipped order'),
(1, 354553, '2021-05-27 11:51:39', 'Shipped order'),
(1, 354553, '2021-05-27 11:51:53', 'Shipped order');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `ski`
--

CREATE TABLE `ski` (
  `productID` int(11) NOT NULL,
  `skiID` int(6) NOT NULL,
  `length` enum('142','147','152','157','162','167','172','177','182','187','192','197','202','207') NOT NULL,
  `weight` enum('20-30','30-40','40-50','50-60','60-70','70-80','80-90','90+') NOT NULL,
  `reservedToOrder` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dataark for tabell `ski`
--

INSERT INTO `ski` (`productID`, `skiID`, `length`, `weight`, `reservedToOrder`) VALUES
(3, 1, '157', '90+', NULL),
(4, 1, '157', '90+', NULL),
(5, 1, '157', '90+', 779075),
(19811, 1, '157', '90+', NULL),
(44649, 1, '157', '90+', NULL);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `skitype`
--

CREATE TABLE `skitype` (
  `skiID` int(6) NOT NULL,
  `type` enum('classic','skate','doublePole') NOT NULL,
  `model` enum('active','activePro','endurance','intrasonic','racePro','raceSpeed','redline') NOT NULL,
  `temperature` enum('cold','warm','regular') DEFAULT NULL,
  `gripSystem` enum('wax','intelliGrip') DEFAULT NULL,
  `description` varchar(200) NOT NULL,
  `historical` tinyint(1) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `retailPrice` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dataark for tabell `skitype`
--

INSERT INTO `skitype` (`skiID`, `type`, `model`, `temperature`, `gripSystem`, `description`, `historical`, `url`, `retailPrice`) VALUES
(1, 'skate', 'active', 'regular', 'wax', 'Designed for easy handling and solid all-around performance.', 1, 'https://i1.adis.ws/i/madshus/madshus_2021_N19343_Active-Skate?w=480&fmt=webp&bg=white&protocol=https', 2600),
(2, 'classic', 'active', 'regular', 'wax', 'Lightweight classic ski for touring and training.', 1, 'https://i1.adis.ws/i/madshus/madshus_1920_active-classic?w=480&fmt=webp&bg=white&protocol=https', 2500),
(3, 'classic', 'active', 'regular', 'intelliGrip', 'Consistent, reliable grip with IntelliGrip.', 1, 'https://i1.adis.ws/i/madshus/madshus_2021_active-intelligrip_1?w=412&fmt=webp&bg=white&protocol=https&dpi=144', 3100),
(4, 'skate', 'activePro', 'regular', 'wax', 'Designed for a more stable glide.', 1, 'https://i1.adis.ws/i/madshus/madshus_1920_active-pro-skate?w=480&fmt=webp&bg=white&protocol=https', 3300),
(5, 'classic', 'activePro', 'regular', 'intelliGrip', 'Designed for groomed track touring and training.', 1, 'https://i1.adis.ws/i/madshus/madshus_1920_active-pro-intelligrip?w=412&fmt=webp&bg=white&protocol=https&dpi=144', 3800),
(6, 'skate', 'endurance', 'regular', 'wax', 'The Endurance Skate is a training and skate touring ski.', 1, 'https://i1.adis.ws/i/madshus/madshus_1920_endurace-skate?w=412&fmt=webp&bg=white&protocol=https&dpi=144', 3600),
(7, 'classic', 'endurance', 'regular', 'wax', 'Ideal choice for fast fitness workouts and long weekend tours.', 1, 'https://i1.adis.ws/i/madshus/madshus_1920_endurace-classic?w=480&fmt=webp&bg=white&protocol=https', 3600),
(8, 'classic', 'endurance', 'regular', 'intelliGrip', 'Designed to go the distance on epic weekend tours.', 1, 'https://i1.adis.ws/i/madshus/madshus_1920_endurace-intelligrip?w=412&fmt=webp&bg=white&protocol=https&dpi=144', 4000),
(9, 'skate', 'intrasonic', 'regular', 'wax', 'High-stability skate ski designed for beginners.', 1, 'https://i1.adis.ws/i/madshus/madshus_1920_intrasonic-skate?w=480&fmt=webp&bg=white&protocol=https', 2100),
(10, 'skate', 'raceSpeed', 'regular', 'wax', 'Perfect for conquering both raceday, and everyday.', 1, 'https://i1.adis.ws/i/madshus/madshus_1920_race-speed-skate?w=412&fmt=webp&bg=white&protocol=https&dpi=144', 4600),
(11, 'classic', 'raceSpeed', 'regular', 'wax', 'Inspired by our successful racing department.', 1, 'https://i1.adis.ws/i/madshus/madshus_1920_race-speed-classic?w=480&fmt=webp&bg=white&protocol=https', 4600),
(12, 'classic', 'raceSpeed', 'regular', 'intelliGrip', 'Inspired by our successful racing department.', 1, 'https://i1.adis.ws/i/madshus/madshus_1920_race-speed-intelligrip?w=412&fmt=webp&bg=white&protocol=https&dpi=144', 5100),
(13, 'skate', 'racePro', 'regular', 'wax', 'High-performance ski which is race proven at the highest level.', 1, 'https://i1.adis.ws/i/madshus/madshus_1920_race-pro-skate?w=412&fmt=webp&bg=white&protocol=https&dpi=144', 5600),
(14, 'classic', 'racePro', 'regular', 'wax', 'Shares its construction with our race-proven world cup skis from 2017.', 1, 'https://i1.adis.ws/i/madshus/madshus_1920_race-pro-classic?w=412&fmt=webp&bg=white&protocol=https&dpi=144', 5600),
(15, 'classic', 'racePro', 'regular', 'intelliGrip', 'Racing and performance ski which features a shorter skin than touring and training models.', 1, 'https://i1.adis.ws/i/madshus/madshus_1920_race-pro-intelligrip?w=412&fmt=webp&bg=white&protocol=https&dpi=144', 6100),
(16, 'skate', 'redline', 'warm', 'wax', 'F3 for slower, softer conditions.', 1, 'https://i1.adis.ws/i/madshus/madshus_2021_redline-3-skate-f3_2?w=412&fmt=webp&bg=white&protocol=https&dpi=144', 7700),
(17, 'skate', 'redline', 'cold', 'wax', 'F2 for harder, faster ski conditions.', 1, 'https://i1.adis.ws/i/madshus/madshus_2021_redline-3-skate-f2_2?w=412&fmt=webp&bg=white&protocol=https&dpi=144', 7700),
(18, 'classic', 'redline', 'warm', 'wax', 'Designed for klister conditions and has shorter pressure zones optimized for warmer snow.', 1, 'https://i1.adis.ws/i/madshus/madshus_2021_redline-3-classic-warm_3?w=412&fmt=webp&bg=white&protocol=https&dpi=144', 7700),
(19, 'classic', 'redline', 'cold', 'wax', 'Designed for hardwax conditions and has longer pressure zones optimized for colder snow.', 1, 'https://i1.adis.ws/i/madshus/madshus_2021_redline-3-classic-cold_2?w=412&fmt=webp&bg=white&protocol=https&dpi=144', 7700),
(20, 'classic', 'redline', 'warm', 'intelliGrip', 'For skiers with good technique, who are looking to go fast, there’s no better skin ski than the Redline 3.0 IntelliGrip®.', 1, 'https://i1.adis.ws/i/madshus/madshus_2021_redline-3-intelligrip_2?w=412&fmt=webp&bg=white&protocol=https&dpi=144', 7900),
(21, 'doublePole', 'redline', 'regular', 'wax', 'The Madshus DP Series is optimized for double pole racing.', 1, 'https://i1.adis.ws/i/madshus/madshus_2021_redline-3-double-pole_2?w=412&fmt=webp&bg=white&protocol=https&dpi=144', 7700);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `store`
--

CREATE TABLE `store` (
  `customerID` int(11) NOT NULL,
  `negotiatedPrice` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dataark for tabell `store`
--

INSERT INTO `store` (`customerID`, `negotiatedPrice`) VALUES
(6, 0.6),
(7, 0.2);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `teamskier`
--

CREATE TABLE `teamskier` (
  `customerID` int(11) NOT NULL,
  `dateOfBirth` date NOT NULL,
  `club` varchar(100) NOT NULL,
  `numberOfSkisYearly` int(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dataark for tabell `teamskier`
--

INSERT INTO `teamskier` (`customerID`, `dateOfBirth`, `club`, `numberOfSkisYearly`) VALUES
(2, '1999-04-01', 'Karasjok Skilag', 4);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `transporter`
--

CREATE TABLE `transporter` (
  `transporterID` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dataark for tabell `transporter`
--

INSERT INTO `transporter` (`transporterID`, `name`) VALUES
(1, 'Bring'),
(2, 'PostNord'),
(3, 'DHL');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auth_token`
--
ALTER TABLE `auth_token`
  ADD PRIMARY KEY (`token`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customerID`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`employeeNumber`);

--
-- Indexes for table `franchise`
--
ALTER TABLE `franchise`
  ADD PRIMARY KEY (`customerID`),
  ADD KEY `customerID` (`customerID`);

--
-- Indexes for table `handlesorder`
--
ALTER TABLE `handlesorder`
  ADD PRIMARY KEY (`date`),
  ADD KEY `orderNumber` (`orderNumber`),
  ADD KEY `employeeNumber` (`employeeNumber`);

--
-- Indexes for table `handlesski`
--
ALTER TABLE `handlesski`
  ADD PRIMARY KEY (`date`),
  ADD KEY `productID` (`productID`);

--
-- Indexes for table `ordercontent`
--
ALTER TABLE `ordercontent`
  ADD PRIMARY KEY (`ID`) USING BTREE,
  ADD KEY `orderNumber` (`orderNumber`),
  ADD KEY `skiID` (`skiID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`orderNumber`),
  ADD KEY `orderNumber` (`orderNumber`),
  ADD KEY `customerID` (`customerID`);

--
-- Indexes for table `productionplan`
--
ALTER TABLE `productionplan`
  ADD PRIMARY KEY (`start_date`),
  ADD KEY `employeeNumber` (`employeeNumber`),
  ADD KEY `skiID` (`skiID`);

--
-- Indexes for table `shipment`
--
ALTER TABLE `shipment`
  ADD PRIMARY KEY (`shipmentNumber`),
  ADD KEY `driverID` (`transporterID`),
  ADD KEY `customerID` (`customerID`);

--
-- Indexes for table `shipmentrecordings`
--
ALTER TABLE `shipmentrecordings`
  ADD PRIMARY KEY (`shipmentNumber`,`date`);

--
-- Indexes for table `ski`
--
ALTER TABLE `ski`
  ADD PRIMARY KEY (`productID`),
  ADD KEY `skiID` (`skiID`),
  ADD KEY `length` (`length`,`weight`),
  ADD KEY `reservedToOrder` (`reservedToOrder`);

--
-- Indexes for table `skitype`
--
ALTER TABLE `skitype`
  ADD PRIMARY KEY (`skiID`);

--
-- Indexes for table `store`
--
ALTER TABLE `store`
  ADD PRIMARY KEY (`customerID`),
  ADD KEY `customerID` (`customerID`);

--
-- Indexes for table `teamskier`
--
ALTER TABLE `teamskier`
  ADD PRIMARY KEY (`customerID`),
  ADD KEY `customerID` (`customerID`);

--
-- Indexes for table `transporter`
--
ALTER TABLE `transporter`
  ADD PRIMARY KEY (`transporterID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ski`
--
ALTER TABLE `ski`
  MODIFY `productID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44650;

--
-- Begrensninger for dumpede tabeller
--

--
-- Begrensninger for tabell `franchise`
--
ALTER TABLE `franchise`
  ADD CONSTRAINT `customerFranchise_FK` FOREIGN KEY (`customerID`) REFERENCES `customer` (`customerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Begrensninger for tabell `handlesorder`
--
ALTER TABLE `handlesorder`
  ADD CONSTRAINT `employeeNumber` FOREIGN KEY (`employeeNumber`) REFERENCES `employee` (`employeeNumber`) ON DELETE CASCADE,
  ADD CONSTRAINT `handlesorder_ibfk_1` FOREIGN KEY (`orderNumber`) REFERENCES `orders` (`orderNumber`) ON DELETE CASCADE;

--
-- Begrensninger for tabell `handlesski`
--
ALTER TABLE `handlesski`
  ADD CONSTRAINT `handlesski_ibfk_2` FOREIGN KEY (`productID`) REFERENCES `ski` (`productID`) ON DELETE CASCADE;

--
-- Begrensninger for tabell `ordercontent`
--
ALTER TABLE `ordercontent`
  ADD CONSTRAINT `ordernrFK` FOREIGN KEY (`orderNumber`) REFERENCES `orders` (`orderNumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `skiID` FOREIGN KEY (`skiID`) REFERENCES `skitype` (`skiID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Begrensninger for tabell `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `customerID` FOREIGN KEY (`customerID`) REFERENCES `customer` (`customerID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
