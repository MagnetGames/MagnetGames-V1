<?php
	/*
	1 = Playstation (official)
	2 = Playstation (unofficial)
	3 = Playstation (tools + utils)
	4 = Playstation 2 (official)
	5 = Playstation 2 (unofficial)
	6 = Playstation 2 (tools + utils)
	7 = Windows (Games)
	8 = Windows (Software)
	9 = MS-DOS (Games)
	10 = MS-DOS (Software)
	11 = Nintendo Entertainment System (official)
	12 = Nintendo Entertainment System (unofficial)
	13 = Super Nintendo Entertainment System (official)
	14 = Super Nintendo Entertainment System (unofficial)
	15 = Nintendo 64 (official)
	16 = Nintendo 64 (unofficial)
	17 = Nintendo GameCube (official)
	18 = Nintendo GameCube (unofficial)
	19 = Nintendo GameCube (tools + utils)
	20 = Sega Master System (official)
	21 = Sega Master System (unofficial)
	22 = Sega Genesis/Megadrive (official)
	23 = Sega Genesis/Megadrive (unofficial)
	24 = Sega Mega CD (official)
	25 = Sega 32X (official)
	26 = Sega Saturn (official)
	27 = Sega Dreamcast (official)
	28 = Sega Dreamcast (unofficial)
	29 = Sega Dreamcast (tools + utils)
	30 = Xbox (official)
	31 = Xbox (unofficial)
	32 = Xbox (tools + utils)
	33 = Nintendo Gameboy (Official)
	34 = Nintendo Gameboy (Tools + Utils)
	35 = Nintendo Gameboy Color (Official)
	36 = Nintendo Gameboy Color (Tools + Utils)
	37 = Nintendo Gameboy Advance (Official)
	38 = Nintendo Gameboy Advance (Unofficial)
	39 = Nintendo Gameboy Advance (Tools + Utils)
	40 = Nintendo DS (Official)
	41 = Nintendo DS (Unofficial)
	42 = Nintendo DS (Tools + Utils)
	43 = Sony PSP (Official)
	44 = Sony PSP (Unofficial)
	45 = Sony PSP (Tools + Utils)
	46 = Commodore 64 (Games)
	47 = Commodore 64 (Software)
	48 = Sega Game Gear (official)
	49 = Sega Game Gear (unofficial)
	50 = PC Engine / TurboGrafx-16 (official)
	51 = PC Engine / TurboGrafx-16 (unofficial)
	52 = PC Engine / TurboGrafx-16 (CD-ROM)
	53 = PC Engine / TurboGrafx-16 (Tools + Utils)

	403 = 403 No Permission
	404 = 404 Not Found!
	500 = 500 Internal Server Error
	
	600 = Guides
	601 = Community Guidelines
	602 = About Us
	603 = Contact/DMCA
	604 = Using Magnet Links
	605 = Using IPFS
	606 = Stats (no html)
	607 = News Archive
	??? = How to make perfect PS1 dumps and burns - CloneCD

	*/
	
	function GenTitleForPageID($pageid)
	{
		switch ($pageid) 
		{
			//Game Consoles
			case 0:
				return "Home Page";
			case 1:
			case 2:
			case 3:
				return "Sony PlayStation";
			case 4:
			case 5:
			case 6:
				return "Sony PlayStation 2";
			case 7:
			case 8:
				return "Microsoft Windows";
			case 9:
			case 10:
				return "MS-DOS";
			case 11:
			case 12:
				return "NES";
			case 13:
			case 14:
				return "SNES";
			case 15:
			case 16:
				return "N64";
			case 17:
			case 18:
			case 19:
				return "GameCube";
			case 20:
			case 21:
				return "Sega Master System";
			case 22:
			case 23:
				return "Genesis";
			case 24:
				return "Sega CD";
			case 25:
				return "Sega 32X";
			case 26:
				return "Sega Saturn";
			case 27:
			case 28:
			case 29:
				return "Sega Dreamcast";
			case 30:
			case 31:
			case 32:
				return "Xbox";
			case 33:
			case 34:
				return "GB";
			case 35:
			case 36:
				return "GBC";
			case 37:
			case 38:
			case 39:
				return "GBA";
			case 40:
			case 41:
			case 42:
				return "NDS";
			case 43:
			case 44:
			case 45:
				return "PSP";
			case 46:
			case 47:
				return "C64";
			case 48:
			case 49:
				return "Sega Game Gear";
			case 50:
			case 51:
			case 53:
				return "PC Engine";
			case 52:
				return "PC Engine CD-ROM 2";
			//Site errors
			case 403:
				return "No Permission!";
			case 404:
				return "Not Found!";
			case 500:
				return "Internal Server Error!";	
			//Main site pages
			case 600:
				return "Guides";			
			case 601:
				return "Community Guidelines";
			case 602:
				return "About Us";
			case 603:
				return "Contact Us";
			case 604:
				return "Using Magnet Links";
			case 605:
				return "Using IPFS";
			case 606:
				return "Stats";
			case 607:
				return "News Archive";
			default:
				return null;
		}
	}
	//Generates a description
	function GenDescForPageID($pageid)
	{
		switch ($pageid) 
		{
			//Game Consoles
			case 0:
				return "Disc Images, Guides, Roms? You want it?\nThen come to MagnetGames!";
			case 1:
				return "The Sony PlayStation (Officially Released) games section!";
			case 2:
				return "The Sony PlayStation (Unofficially Released) games section!";
			case 3:
				return "The Sony PlayStation (Tools & Utilities) section!";
			case 4:
				return "The Sony PlayStation 2 (Officially Released) games section!";
			case 5:
				return "The Sony PlayStation 2 (Unofficially Released) games section!";
			case 6:
				return "The Sony PlayStation 2 (Tools & Utilities) section!";
			case 7:
				return "Games released on Microsoft Windows!";
			case 8:
				return "Software released on Microsoft Windows!";
			case 9:
				return "Games released on MS-DOS!";
			case 10:
				return "Software released on MS-DOS!";
			case 11:
				return "The Nintendo Entertainment System (Officially Released) games section!";
			case 12:
				return "The Nintendo Entertainment System (Unofficially Released) games section!";
			case 13:
				return "The Super Nintendo Entertainment System (Officially Released) games section!";
			case 14:
				return "The Super Nintendo Entertainment System (Unofficially Released) games section!";
			case 15:
				return "The Nintendo 64 (Officially Released) games section!";
			case 16:
				return "The Nintendo 64 (Unofficially Released) games section!";
			case 17:
				return "The Nintendo GameCube (Officially Released) games section!";
			case 18:
				return "The Nintendo GameCube (Unofficially Released) games section!";
			case 19:
				return "The Nintendo GameCube (Tools & Utilities) games section!";
			case 20:
				return "The Sega Master System (Officially Released) games section!";
			case 21:
				return "The Sega Master System (Unofficially Released) games section!";
			case 22:
				return "The Sega Genesis / Mega Drive (Officially Released) games section!";
			case 23:
				return "The Sega Genesis / Mega Drive (Unofficially Released) games section!";
			case 24:
				return "The Sega Mega CD games section!";
			case 25:
				return "The Sega 32X games section!";
			case 26:
				return "The Sega Saturn games section!";
			case 27:
				return "The Sega Dreamcast (Officially Released) games section!";
			case 28:
				return "The Sega Dreamcast (Unofficially Released) games section!";
			case 29:
				return "The Sega Dreamcast (Tools & Utilities) games section!";
			case 30:
				return "The Xbox (Officially Released) games section!";
			case 31:
				return "The Xbox (Unofficially Released) games section!";
			case 32:
				return "The Xbox (Tools & Utilities) games section!";
			case 33:
				return "The Nintendo Game Boy games section!";
			case 34:
				return "The Nintendo Game Boy (Tools & Utilities) games section!";
			case 35:
				return "The Nintendo Game Boy Color games section!";
			case 36:
				return "The Nintendo Game Boy Color (Tools & Utilities) games section!";
			case 37:
				return "The Nintendo Game Boy Advance (Officially Released) games section!";
			case 38:
				return "The Nintendo Game Boy Advance (Unofficially Released) games section!";
			case 39:
				return "The Nintendo Game Boy Advance (Tools & Utilities) games section!";
			case 40:
				return "The Nintendo DS (Officially Released) games section!";
			case 41:
				return "The Nintendo DS (Unofficially Released) games section!";
			case 42:
				return "The Nintendo DS (Tools & Utilities) games section!";
			case 43:
				return "The Sony PlayStation Portable (Officially Released) games section!";
			case 44:
				return "The Sony PlayStation Portable (Unofficially Released) games section!";
			case 45:
				return "The Sony PlayStation Portable (Tools & Utilities) games section!";
			case 46:
				return "Games released on the Commodore 64!";
			case 47:
				return "Software released on the Commodore 64!";
			case 48:
				return "The Sega Game Gear (Officially Released) games section!";
			case 49:
				return "The Sega Game Gear (Unofficially Released) games section!";
			case 50:
				return "The PC Engine / TurboGrafx-16 (Officially Released) games section!";
			case 51:
				return "The PC Engine / TurboGrafx-16 (Unofficially Released) games section!";
			case 52:
				return "The PC Engine / TurboGrafx-CD (CD-ROM 2) games section!";
			case 53:
				return "The PC Engine / TurboGrafx-16 (Tools & Utilities) games section!";
			
			//Site errors
			case 403:
				return "403 No Permission!";
			case 404:
				return "404 Page Not Found!";
			case 500:
				return "500 Internal Server Error!";	
			//Main site pages
			case 600:
				return "The section for MagnetGames small Guides and Tutorials!";			
			case 601:
				return "The official MagnetGames Site Guidelines and Privacy Policy.";
			//Don't worry about 602, as that's the default anyway
			case 603:
				return "MagnetGames site owner contact information.";
			case 604:
				return "The MagnetGames official guide on using Magnet Links.";
			case 605:
				return "The MagnetGames official guide on using the InterPlanetary File System (IPFS).";
			case 606:
				return "Sitewide statistics for Magnet-Games.com!";
			case 607:
				return "What was new in the past for Magnet-Games.com?";
			default:
				return "MagnetGames is a metadata sharing gaming website dedicated for Retro Gamers around the globe!";
		}
	}
?>