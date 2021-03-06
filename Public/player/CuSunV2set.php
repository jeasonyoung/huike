<?php
session_start()
?>
<CuPlayer>
      <Player_Set 
		JcScpBufferTime = "5"
 		JcScpVolume = "75"
		JcScpCode = "utf8"
 		JcScpImgDisplay = "no"
		JcScpAutoHideControl="yes"
		JcScpControlHideTime="0.5"
		JcScpControlHeight="40"	
		JcScpShowList= "yes"
		JcScpAutoRepeat = "no"
		JcScpAutoPlay = "yes"
		JcScpsetMode = "1"
		JcScpAFrontCanClose = "no"
		JcScpShowRightmenu = "yes"
		JcScpShareMode = "JcScpVideoPath"
	/>
      <Logo_Set
 		JcScpLogoDisplay = "yes"
 		JcScpLogoPath = "<?php echo $_SESSION['logo']; ?>"
 		JcScpLogoPosition = "top-right"
		JcScpLogoWidth = "281"
 		JcScpLogoHeight = "92"
 		JcScpLogoAlpha = "1"
	/>
      <Flashvars_Set
		JcScpServer =""
		JcScpVideoPath = ""
		JcScpVideoPathHD = ""
		JcScpImg ="images/startpic.jpg" 
        
		JcScpStarTime = "0" 
		JcScpEndTime = "0"
		
		ShowJcScpAFront = "no"
		JcScpCountDowns = "5"
		JcScpCountDownsPosition = "none"
		JcScpAFrontW = "650"
		JcScpAFrontH = "418"
		JcScpAFrontPath = "images/a650x418.swf"
		JcScpAFrontLink = "http://demo.cuplayer.com/CuSunPlayer/demo1.html"
		
		ShowJcScpAVideo= "no"
		JcScpAVideoPath= "up.flv"	
		JcScpAVideoLink= "http://demo.cuplayer.com/CuSunPlayer/demo2.html"
		
		ShowJcScpAPause = "no"
		JcScpAPausePath = "images/a300x250.swf"
		JcScpAPauseW = "300"
		JcScpAPauseH = "250"
		JcScpAPauseLink= "http://demo.cuplayer.com/CuSunPlayer/demo3.html"
		
		ShowJcScpACorner = "no"
		JcScpACornerPath= "images/a370x250.swf"
		JcScpACornerW = "80"
		JcScpACornerH = "50"
		JcScpACornerPosition = "top-right"
		JcScpACornerLink = "http://demo.cuplayer.com/CuSunPlayer/demo4.html"
 
		ShowJcScpAEnd = "no"
		JcScpAEndPath = "images/a400x300.swf"
		JcScpAEndW = "400"
		JcScpAEndH = "300"
		JcScpAEndLink= "http://demo.cuplayer.com/CuSunPlayer/demo5.html"
		
	 />

	 <SkinColor_Set
 		JcScpBackcolor = "0x000000"
        JcScpBackcolortop = "0x353535"
        JcScpLightcolor = "0xcfcfcf"
        
        JcScpFontcolor = "0xffffff"
        JcScptimebg = "0x393939"
        
        JcScpLoadbar = "0x00a0e9"
        JcScpLoaded = "0x4d4b4b"
        JcScpLoadbg = "0x000000"
        JcScpPlayBtn = "0x2d2d2d"
		
        JcScpBar = "0xffffff" 
	 />
</CuPlayer>

