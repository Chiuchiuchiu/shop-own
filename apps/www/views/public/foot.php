
 <!DOCTYPE html>
 <html>
     <head>
         <title></title>
     </head>
     <body>
           <style>
    .footNav{
        position:fixed;
        bottom:0;
        height:50px;
        background-color:#ffffff;
        width:100%;
    }
    .footNav >ul li{
        float:left;
        width:25%;
        padding-top:5px;
        text-align:center;

    }
    .footNav >ul li a{
        display:inline-block;
    }
    .footNav >ul .account{
        float:left;
    }
     .footNav >ul li .icon{
        display:block;
        width:24px;
        height:24px;
        background-size:100%;
        margin-left:2px;
     }
      .footNav >ul li .icon p {
          display:block;
          width:28px;
          padding-top:24px;
      }
    .footNav >ul li:nth-of-type(1) .icon{
        background:url('/static/images/ico/shouye.png');
        background-size:100%;
    }
    .footNav >ul li:nth-of-type(2) .icon{
        background:url('/static/images/ico/shenghuo.png') no-repeat 0px 0;
        background-size:100%;
    }
    .footNav >ul li:nth-of-type(3) .icon{
        background:url('/static/images/ico/shangcheng.png') no-repeat 0px 0;
        background-size:100%;
    }
    .footNav >ul li:nth-of-type(4) .icon{
        background:url('/static/images/ico/mySelf.png') no-repeat 0px 0;
        background-size:100%;
    }
    
    .footNav >ul li .current>p{
        color:#FCC713;
    }
    .footNav >ul li:nth-of-type(1) .icon.current{
         background:url('/static/images/ico/shouye1.png') no-repeat 0px 0;
         background-size:100%;
    }
    .footNav >ul li:nth-of-type(2) .icon.current{
         background:url('/static/images/ico/shenghuo1.png') no-repeat 0px 0;
         background-size:100%;
    }
    .footNav >ul li:nth-of-type(3) .icon.current{
         background:url('/static/images/ico/shangcheng1.png') no-repeat 0px 0;
         background-size:100%;
    }
    .footNav >ul li:nth-of-type(4) .icon.current{
         background:url('/static/images/ico/mySelf1.png') no-repeat 0px 0;
         background-size:100%;
    }
</style>
 <footer></footer>
           <div class="footNav">
               <ul>
                   <li class="home">
                       <a href="/default/">
                           <i class="icon <?php  if($currentPage == 'home') {echo 'current';}  ?> ">
                                                        
                           </i>
                           <p>首页</p>
                       </a>
                   </li>
                   <li class="owner">
                       <a href="/my-life/">
                           <i class="icon   <?php  if($currentPage == 'mylife') {echo 'current';} ?> ">
                        </i>
                          <p>生活</p>
                       </a>
                   </li>
                   <li class="account ">
                       <a href="/shopping/">
                           <i class="icon  <?php  if($currentPage == 'shopping') {echo 'current';}  ?>">
                            
                           </i>
                           <p>商城</p>
                          
                       </a>
                   </li>
                   <li class="account ">
                       <a href="/house/">
                           <i class="icon  <?php  if($currentPage == 'house') {echo 'current';}  ?>">
                             
                           </i>
                           <p>我的</p>
                           
                       </a>
                   </li>
               </ul>
           </div>
</footer>   
     </body>
 </html>
 


