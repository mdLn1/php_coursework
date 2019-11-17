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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="bootstrap.min.js"></script>
    <style>
        .content-wrapper {
            margin: 0 auto;
            max-width: 30em;
            width: 80%;
        }

        .dropbtn {
            color: white;
            border: none;
            cursor: pointer;
        }

        /* The container <input> - needed to position the dropdown content */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        /* Dropdown Content (Hidden by Default) */
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 140px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        /* Links inside the dropdown */
        .dropdown-content span {
            color: black;
            cursor: pointer;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        /* Change color of dropdown links on hover */
        .dropdown-content span:hover {
            background-color: #111111;
            color: white;
        }

        /* Show the dropdown menu on hover */
        .dropdown:hover .dropdown-content {
            display: block;
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
            <form class="form-inline my-2 my-lg-0" style="position: absolute; right: 0; top: 0;" id="search-form">
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
                <div class="form-group" style="display: inline-block; position: relative;">
                    <input class="form-control mr-sm-2" type="text" id="search-content" style="padding-left: 1rem; margin-left: 1rem;" placeholder="Search">
                    <span id="search-error" style="position:absolute; color:red; top: -1.5rem; left: 1rem; display:none;">Only digits allowed!</span>
                    <span id="last-search" style="display:none; position:absolute; bottom: -1.5rem; left: 1rem;">Last search: <span id="last-search-text" style="text-decoration:underline; color:blue; cursor: pointer;"></span></span>
                </div>
                <input type="submit" class="btn btn-success" value="Search" />
                </input>
            </form>
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
    <script>
        $(document).ready(function() {
            var pageNumber = 1,
                totalPages = 1,
                queryString = "",
                previousQuery = "";

            $("tbody").on("click", ".stdid", function() {
                window.location = 'studentRecord.php?studentid=' + $(this).text().trim();
            });

            $("tbody").on("click", ".grpid", function() {
                window.location = 'groupRecord.php?group=' + $(this).text().trim();
            });

            function checkPaging() {
                if (totalPages === 1) {
                    document.getElementById("next").setAttribute("class", "page-item disabled");
                    document.getElementById("previous").setAttribute("class", "page-item disabled");
                } else if (pageNumber === totalPages && pageNumber > 1) {
                    document.getElementById("next").setAttribute("class", "page-item disabled");
                    document.getElementById("previous").className = "page-item";
                } else if (pageNumber === 1) {
                    document.getElementById("next").className = "page-item";
                    document.getElementById("previous").setAttribute("class", "page-item disabled");
                } else {
                    document.getElementById("next").className = "page-item";
                    document.getElementById("previous").className = "page-item";
                }
                document.getElementById("current-page").innerHTML = `Page ${pageNumber} of ${totalPages}`;
            }

            $("#search-content").on("keyup paste", function(event) {
                let re = /[^0-9]/;
                if (re.test(event.target.value)) {
                    el = event.target.value.match(/[0-9]+/);
                    if(el !== null) {
                    $(this).val(el[0]);
                    $(this).css("border", "3px solid red");
                    $("#search-error").css("display", "block");
                    setTimeout(function() {
                        $("#search-error").css("display", "none");
                        $("#search-content").css("border", "0");
                    }, 3000);
                } else {
                    $(this).val("");
                }
            }
            })

            $(".search-option").on("click", function(event) {
                event.preventDefault();
                let searchCriteria = event.target.innerHTML;
                $("#search-criteria").html(searchCriteria);
                if (searchCriteria === "Grade") {
                    $("#sorting-container").css("display", "block");
                    $("#over-under").css("display", "block");
                } else {
                    $("#sorting-container").css("display", "none");
                    $("#over-under").css("display", "none");
                }
            })
            $(".sorting-option").on("click", function(event) {
                event.preventDefault();
                let sortingCriteria = event.target.innerHTML;
                $("#sorting-criteria").html(sortingCriteria);
            })
            $(".grade-option").on("click", function(event) {
                event.preventDefault();
                let gradeCriteria = event.target.innerHTML;
                $("#grade-criteria").html(gradeCriteria);
            })

            $("#last-search-text").on("click", function() {
                $("#search-content").val($(this).text());
                $("#last-search").css("display", "none");
            })

            $("#search-form").on("submit", function(event) {
                event.preventDefault();
                var searchedFor = document.getElementById("current-search-results");
                let searchValue = $("#search-content").val();
                let searchCriteria = $("#search-criteria").text();

                if (searchCriteria === "Grade") {
                    let gradeCriteria = $("#grade-criteria").text();
                    let sortingCriteria = $("#sorting-criteria").text();
                    searchedFor.innerHTML = `Search for grades ${gradeCriteria} ${searchValue} in ${sortingCriteria}`;
                    queryString = `?grade=${searchValue}&filter=${gradeCriteria.toLowerCase()}&sort=${sortingCriteria.toLowerCase()}`;
                } else {
                    queryString = `?studentId=${searchValue}`;
                    searchedFor.innerHTML = `Search students IDs containing "${searchValue}"`;
                }
                searchRecords(true);
                $("#search-content").val("");
            });

            $("#next span").on("click", function() {
                ++pageNumber;
                searchRecords();
            });
            $("#previous span").on("click", function() {
                --pageNumber;
                searchRecords();
            });
            $("#first span").on("click", function() {
                pageNumber = 1;
                searchRecords();
            });
            $("#last span").on("click", function() {
                pageNumber = totalPages;
                searchRecords();
            });

            function searchRecords(newSearch = false) {
                $.ajax({
                    url: 'searchRequests.php' + queryString,
                    data: {
                        pageNo: pageNumber
                    },
                    type: 'GET',
                    dataType: 'JSON',
                    success: function(output, status, xhr) {
                        $("#results-found").html(output.resultsFound);
                        totalPages = parseInt(output["pages"]);
                        pageNumber = parseInt(output["currentPage"]);
                        $("#table-body").empty();
                        let tableBody = document.getElementById("table-body");
                        output["queryResults"].forEach((el, i) => {
                            let row = document.createElement("TR");
                            let th = document.createElement("TH");
                            let td1 = document.createElement("TD"),
                                td2 = document.createElement("TD");
                            th.setAttribute("scope", row);
                            let span = document.createElement("SPAN");
                            span.setAttribute("class", "stdid");
                            span.innerText = el["ID"];
                            td1.appendChild(span);
                            td2.innerHTML = `<span class="grpid">${el["group_number"]}</span>`;
                            th.innerHTML = i + 1;
                            row.appendChild(th);
                            row.appendChild(td1);
                            row.appendChild(td2);
                            tableBody.appendChild(row);
                        });
                        checkPaging();
                    },
                    error: function(xhr, status, error) {
                        $("#top-alert").css("display", "block");
                        $("#error-message").text(xhr.responseJSON.message);
                        setTimeout(function() {
                            $("#top-alert").css("display", "none");
                        }, 5000);

                    }
                });
                if (newSearch) {
                    $.ajax({
                        url: 'functionality/getLastSearch.php',
                        type: 'GET',
                        dataType: 'JSON',
                        success: function(result) {
                            if (result.cookie !== "empty") {
                                $("#last-search").css("display", "block");
                                $("#last-search-text").text(result.cookie);
                            } else {
                                $("#last-search").css("display", "none");
                            }
                        }
                    });
                }
            }
            searchRecords();
        });
    </script>
    <?php if (!(isset($_COOKIE["CookiesAccepted"]) && $_COOKIE["CookiesAccepted"] === "yes")) include("pageContent/cookieAlert.php") ?>

</body>

</html>