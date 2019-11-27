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
            
            $(".dropdown").on("click", function(e) {
                e.preventDefault();
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
                document.getElementById("current-page").innerText = `Page ${pageNumber} of ${totalPages}`;
            }

            $("#search-content").on("keyup paste", function(event) {
                let re = /[^0-9]/;
                if (re.test(event.target.value)) {
                    el = event.target.value.match(/[0-9]+/);
                    if (el !== null) {
                        $(this).val(el[0]);
                        
                    } else {
                        $(this).val("");
                    }
                    $(this).css("border", "3px solid red");
                        $("#search-error").css("display", "block");
                        setTimeout(function() {
                            $("#search-error").css("display", "none");
                            $("#search-content").css("border", "0");
                        }, 5000);
                }
            });

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

            $("#search-button").on("click", function(event) {
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
                if(!searchValue){
                    queryString = "";
                    pageNumber = 1;
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
            $(".group-option").on("click", function(event){
                event.preventDefault();
                window.location = 'groupRecord.php?group=' + $(this).text().trim();
            });
            function getLastSearch() {
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
            };

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
                        $("#table-body").empty();
                        if (output.resultsFound === 0) {
                            $("#current-page").hide();
                            $(".pagination").hide();
                        } else {
                            $("#current-page").show();
                            $(".pagination").show();
                            totalPages = parseInt(output["pages"]);
                            pageNumber = parseInt(output["currentPage"]);
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
                        }
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
                    getLastSearch();
                }
            }
            searchRecords();
            getLastSearch();
        });