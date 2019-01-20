<?php

echo '<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">Doxbin</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li><a href="postdox.php">Post Dox</a></li>
                <li><a href="faq.php">FAQ</a></li>
                <li><a href="proscription.php">Proscription</a></li>
                <li><a href="privacy.php">Privacy Policy</a></li>
            </ul>
            <div class="col-sm-3 col-md-3 pull-right">
            <form class="navbar-form" method="GET" action="search.php" role="search">
                <div class="input-group">
                    <input type="hidden" name="action" value="SEARCH" />
                    <input type="text" name="keyword"  class="form-control" placeholder="Search">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="submit">Go!</button>
                    </span>
                </div>
            </form>
        </div>
        </div>
        <!--/.nav-collapse -->
    </div>
    <!--/.container-fluid -->
</nav>';
