<?php
session_start();
include "checks/loggedIn.php";
include "checks/studentLogged.php";

?>
<!DOCTYPE html>
<html>

<head>
    <title>Tutor students checks</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="custom.css" rel="stylesheet">
    
    <script src="bootstrap.min.js"></script>
    <style>
        .content-wrapper {
            margin: 0 auto;
            max-width: 30em;
            width: 80%;
        }

        i {
            border: solid white;
            border-width: 0 3px 3px 0;
            display: inline-block;
            padding: 3px;
            margin-top: -13px;
            margin-left: .5rem;
        }

        .down {
            transform: rotate(45deg);
            -webkit-transform: rotate(45deg);
        }

        li:hover {
            cursor: pointer;
        }

        li.disabled:hover {
            cursor: not-allowed;
        }

        .pagination {
            justify-content: center;
        }
        .form-inline.my-2.my-lg-0 {
            position: absolute; 
            right: 0; 
            top: 0;
        }
        #input-form {
            display: inline-block; 
            position: relative;
        }
        #search-content {
            padding-left: 1rem;
            margin-left: 1rem;
        }
        @media only screen and (max-width: 766px) {
            #search-form {
                position:relative;
                text-align:center;
                margin:0 auto;
            }
            
        }
        @media only screen and (max-width: 600px) {
            #search-form > div {
                display: block;
            }
            .dropdown {
                margin-bottom: .5rem;
            }
            .form-group {
                margin-bottom: 0;
            }
            #input-form{
                padding-right:1rem; 
                padding-left: 1rem;
            }
            #search-content{
                margin-left:0; 
            }
        }
        @media only screen and (max-width: 400px) {
            #input-form{
                padding-left: 0;
                margin-bottom: 1rem;
            }
            #submit-div {
                margin-top:.5rem;
            }
        }
        
    </style>

</head>

<body>
    <?php include("pageContent/navbar.php") ?>
    <div style="padding: 1rem;">
        <div class="alert alert-danger" id="top-alert" role="alert" style="display: none;">
            <h4 class="alert-heading">Error! Request not completed</h4>
            <p id="error-message"></p>
        </div>
        <div style="position: relative; margin-bottom: 1rem; padding-bottom: 1rem;">
            <div class="form-inline my-2 my-lg-0" id="search-form">
                <div class="dropdown">
                    <button class="dropbtn btn btn-secondary">Search By: <span id="search-criteria">ID</span> <i class="arrow down"></i></button>
                    <div class="dropdown-content">
                        <span class="search-option">ID</span>
                        <span class="search-option">Grade</span>
                    </div>
                </div>
                <div class="dropdown" id="over-under" style="display: none; margin-left: .5rem; ">
                    <button class="dropbtn btn btn-secondary"><span id="grade-criteria">Over</span> <i class="arrow down"></i></button>
                    <div class="dropdown-content">
                        <span class="grade-option">Over</span>
                        <span class="grade-option">Under</span>
                    </div>
                </div>
                <div class="form-group" id="input-form">
                    <input class="form-control mr-sm-2" type="text" id="search-content" placeholder="Search">
                    <span id="search-error" style="position:absolute; color:red; top: -1.5rem; left: 1rem; display:none;">Only digits allowed!</span>
                    <span id="last-search" style="display:none; position:absolute; bottom: -1.5rem; left: 1rem;">Last search: <span id="last-search-text" style="text-decoration:underline; color:blue; cursor: pointer;"></span></span>
                </div>
                <div id="submit-div">
                <button type="button" class="btn btn-success" id="search-button">Search</button>
                    </div>
            </div>
        </div>
        <div id="sorting-container" style="display: none;">
            <div class="dropdown">
                <button class="dropbtn btn btn-secondary">Sorting: <span id="sorting-criteria">Ascending</span> <i class="arrow down"></i></button>
                <div class="dropdown-content">
                    <span class="sorting-option">Ascending</span>
                    <span class="sorting-option">Descending</span>
                </div>
            </div>
        </div>
        <div>
            <h4 id="current-search-results">Search all</h4>
            <h3><span id="results-found"></span> results</h3>
        </div>
        <div id="container-table">
            <h1 style="margin-bottom: 1rem;">Students</h1>
            <div class="content-wrapper">
                <table class="table">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Student's ID</th>
                            <th scope="col">Group Number</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">

                    </tbody>
                </table>
            </div>
            <ul class="pagination">
                <li class="page-item" id="first"><span class="page-link">First</span></li>
                <li class="page-item" id="previous">
                    <span class="page-link">Prev</span>
                </li>
                <li class="page-item" id="next">
                    <span class="page-link">Next</span>
                </li>
                <li class="page-item" id="last"><span class="page-link">Last</span></li>
            </ul>
            <div>
                <span id="current-page"></span>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="scriptsjs/tutor.js">
    </script>
    
    <?php if (!(isset($_COOKIE["CookiesAccepted"]) && $_COOKIE["CookiesAccepted"] === "yes")) include("pageContent/cookieAlert.php") ?>

</body>

</html>