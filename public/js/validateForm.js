$(document).ready(function () {

    $("#alertRegister").hide();
    $("#formRegister").on("submit", function (event) {
        event.preventDefault();
        var form = $("#formRegister");
        var values = form.serialize();
        $.ajax({
            type: "POST",
            url: "/register",
            data: values,
            datatype: "json",
            success: function (response) {
                response = JSON.parse(response);
                $("#alertRegister").html(response.message);
                $("#alertRegister").show("2000");

                if (response.success == true) {
                    $("#formRegister")[0].reset();
                }
            },
            error: function (response) {
                $("#alertRegister").text("Une erreur inattendue est survenue...");
            },
        });
    });

    $("#alertContact").hide();
    $("#formContact").on("submit", function (event) {
        event.preventDefault();
        var form = $("#formContact");
        var values = form.serialize();
        $.ajax({
            type: "POST",
            url: "/contact",
            data: values,
            datatype: "json",
            success: function (response) {
                response = JSON.parse(response);
                $("#alertContact").html(response.message);
                $("#alertContact").show();

                if (response.success == true) {
                    $("#formContact")[0].reset();
                }
            },
            error: function (response) {
                $("#alertContact").text("Une erreur inattendue est survenue...");
            },
        });
    });

    $("#alertLogin").hide();
    $("#formLogin").on("submit", function (event) {
        event.preventDefault();
        var form = $("#formLogin");
        var values = form.serialize();
        $.ajax({
            type: "POST",
            url: "/login",
            data: values,
            datatype: "json",
            success: function (response) {
                response = JSON.parse(response);
                $("#alertLogin").html(response.message);
                $("#alertLogin").show("2000");

                if (response.success == true) {

                    $("#formLogin")[0].reset();
                    window.location.href = "/dashboard";

                }
            },
            error: function (response) {
                $("#alertLogin").text("Une erreur inattendue est survenue...");
            },
        });
    });

    $("#alertEditProfile").hide();
    $("#formEditProfile").on("submit", function (event) {
        event.preventDefault();
        var form = $("#formEditProfile");
        var values = form.serialize();
        $.ajax({
            type: "POST",
            url: "/editProfile",
            data: values,
            datatype: "json",
            success: function (response) {
                response = JSON.parse(response);
                $("#alertEditProfile").html(response.message);
                $("#alertEditProfile").show("2000");
                if (response.success == true) {
                    setTimeout(function () {
                        location.reload();
                    }, 2000)
                }
            },
            error: function (response) {
                $("#alertEditProfile").text("Une erreur inattendue est survenue...");
            },
        });
    });

    function isJson(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

    $("#alertEditPicture").hide();
    $("#formEditPicture").on("submit", function (event) {
        event.preventDefault();
        var form = $("#formEditPicture");
        var values = new FormData(document.getElementById("formEditPicture"));
        $.ajax({
            type: "POST",
            enctype: "multipart/form-data",
            url: "/editPicture",
            data: values,
            contentType: false,
            processData: false,
            datatype: "json",
            success: function (response) {
                if (isJson(response)) {
                    response = JSON.parse(response);
                    $("#alertEditPicture").html(response.message);
                    $("#alertEditPicture").show("2000");
                    if (response.success == true) {
                        location.reload();
                    }
                }
                else {
                    $("#alertEditPicture").html(response);
                    $("#alertEditPicture").show("2000");
                }
            },
            error: function (response) {
                $("#alertEditPicture").text("Une erreur inattendue est survenue...");
            },
        });
    });

    $("#alertLostPassword").hide();
    $("#formLostPassword").on("submit", function (event) {
        event.preventDefault();
        var form = $("#formLostPassword");
        var values = form.serialize();
        $.ajax({
            type: "POST",
            url: "/lostPassword",
            data: values,
            datatype: "json",
            success: function (response) {
                response = JSON.parse(response);
                $("#alertLostPassword").html(response.message);
                $("#alertLostPassword").show("2000");

            },
            error: function (response) {
                $("#alertLostPassword").text("Une erreur inattendue est survenue...");
            },
        });
    });

    $("#alertRenewPassword").hide();
    $("#formRenewPassword").on("submit", function (event) {
        event.preventDefault();
        var form = $("#formRenewPassword");
        var values = form.serialize();
        $.ajax({
            type: "POST",
            url: "/savePassword",
            data: values,
            datatype: "json",
            success: function (response) {
                response = JSON.parse(response);
                $("#alertRenewPassword").html(response.message);
                $("#alertRenewPassword").show("2000");
                if (response.success == true) {
                    setTimeout(function () {
                        window.location.href = "/";
                    }, 2000)
                }
            },
            error: function (response) {
                $("#alertRenewPassword").text("Une erreur inattendue est survenue...");
            },
        });
    });

    $("#alertPostAdd").hide();
    $("#formPostAdd").on("submit", function (event) {
        event.preventDefault();
        var form = $("#formPostAdd");
        var values = new FormData(document.getElementById("formPostAdd"));
        $.ajax({
            type: "POST",
            enctype: "multipart/form-data",
            url: "/postAdd",
            data: values,
            contentType: false,
            processData: false,
            datatype: "json",
            success: function (response) {
                if (isJson(response)) {
                    response = JSON.parse(response);
                    $("#alertPostAdd").html(response.message);
                    $("#alertPostAdd").show("2000");
                    if (response.success == true) {
                        location.reload();
                    }
                }
                else {
                    $("#alertPostAdd").html(response);
                    $("#alertPostAdd").show("2000");
                }
            },
            error: function (response) {
                $("#alertPostAdd").text("Une erreur inattendue est survenue...");
            },
        });
    });



    $("#alertPostEdit").hide();
    $("#formPostEdit").on("submit", function (event) {
        event.preventDefault();
        var form = $("#formPostEdit");
        var values = new FormData(document.getElementById("formPostEdit"));
        $.ajax({
            type: "POST",
            enctype: "multipart/form-data",
            url: "/postEdit",
            data: values,
            contentType: false,
            processData: false,
            datatype: "json",
            success: function (response) {
                if (isJson(response)) {
                    response = JSON.parse(response);
                    $("#alertPostEdit").html(response.message);
                    $("#alertPostEdit").show("2000");
                    if (response.success == true) {
                        window.location.href = "/postManager";
                    }
                }
                else {
                    $("#alertPostEdit").html(response);
                    $("#alertPostEdit").show("2000");
                }
            },
            error: function (response) {
                $("#alertPostEdit").text("Une erreur inattendue est survenue...");
            },
        });
    });

    $(".deleteButton").on("click", function (event) {
        event.preventDefault();
        var $this = $(this);
        var parent_id = $this.data("id");
        var title = $this.data("title");
        $("#deleteButtonConfirmation").val(parent_id);
        $("#deleteTitleConfirmation").html(title);
        
    });

    $(".userShow").on("click", function (event) {
        event.preventDefault();
        var $this = $(this);
        var name = $this.data("name");
        var type = $this.data("type");
        var registration = $this.data("registration");
        var connection = $this.data("connection");
        var status = $this.data("status");
        var email = $this.data("email");
        var phone = $this.data("phone");
        var cv = $this.data("cv");
        var twitter = $this.data("twitter");
        var github = $this.data("github");
        var linkedin = $this.data("linkedin");

        if(status==1) 
        {
            status = "<span class='badge badge-primary'>Actif</span>";
        }
        else
        {
            status = "<span class='badge badge-danger'>Inactif</span>";
        }
        
        $("#detailName").html(name);
        $("#detailType").html(type);
        $("#detailRegistration").html(registration);
        $("#detailLastConnection").html(connection);
        $("#detailStatus").html(status);
        $("#detailEmail").html(email);
        $("#detailPhone").html(phone);
        $("#detailCV").html(cv);
        $("#detailTwitter").html(twitter);
        $("#detailGitHub").html(github);
        $("#detailLinkedIn").html(linkedin);
        
    });

    $(".validateButton").on("click", function (event) {
        event.preventDefault();
        var $this = $(this);
        var parent_id = $this.data("id");
        var title = $this.data("title");
        $("#validateButtonConfirmation").val(parent_id);
        $("#validateTitleConfirmation").html(title);
        
    });

    $(".disableButton").on("click", function (event) {
        event.preventDefault();
        var $this = $(this);
        var parent_id = $this.data("id");
        var title = $this.data("title");
        $("#disableButtonConfirmation").val(parent_id);
        $("#disableTitleConfirmation").html(title);
        
    });

    $("#alertPostDelete").hide();
    $(".formPostDelete").on("click", function (event) {
        event.preventDefault();

            var id = $(this).val();
            values = { "idPostDelete": id };
            $.ajax({
                type: "POST",
                url: "/postDelete",
                data: values,
                datatype: "json",
                success: function (response) {
                    response = JSON.parse(response);
                    $("#alertPostDelete").html(response.message);
                    $("#alertPostDelete").show("2000");
                    $("#post-"+ id).hide("slow");
                    $("#delete").modal("hide");
                    location.reload();
                },
                error: function (response) {
                    $("#alertPostDelete").text("Une erreur inattendue est survenue...");
                },
            });

    });

    $("#alertComment").hide();
    $("#formComment").hide();

    $(".reply").on("click", function (event) {
        event.preventDefault();

        var $form = $("#formComment");
        var $alert = $("#alertComment");
        var $this = $(this);
        var parent_id = $this.data("id");
        var $comment = $("#comment-" + parent_id);
        $form.show();
        if (parent_id != 0) {
            $form.find("label").text("Répondre à ce commentaire");
        }
        else {
            $form.find("label").text("Ecrire un nouveau commentaire");
        }

        $("#parent_id").val(parent_id);
        $alert.after($alert);
        $comment.after($form);

    });



    $("#alertComment").hide();
    $("#formComment").on("submit", function (event) {
        event.preventDefault();
        var form = $("#formComment");
        var values = new FormData(document.getElementById("formComment"));
        var slug = values.get("slug");
        var parent_id = values.get("parent_id");
        $.ajax({
            type: "POST",
            url: "/commentAdd",
            processData: false,
            contentType: false,
            cache: false,
            data: values,
            datatype: "json",
            success: function (response) {
                response = JSON.parse(response);
                $("#alertComment-"+parent_id).html(response.message);
                $("#alertComment-"+parent_id).show("2000");
                setTimeout(function () {
                   window.location.href = "/post/" + slug;
                }, 2000)
            },
            error: function (response) {
                $("#alertComment").text("Une erreur inattendue est survenue...");
            },
        });

    });

    $("#alertComment").hide();
    $(".formCommentValidate").on("click", function (event) {
        event.preventDefault();
        
            var id = $(this).val();
            values = { "idCommentValidate": id };
            $.ajax({
                type: "POST",
                url: "/commentValidate",
                data: values,
                datatype: "json",
                success: function (response) {
                    response = JSON.parse(response);
                    $("#alertComment").html(response.message);
                    $("#alertComment").show("2000");
                    $("#validate").modal("hide");
                    $("#comment-"+ id).hide("slow");
                },
                error: function (response) {
                    $("#alertComment").text("Une erreur inattendue est survenue...");
                },
            });
        

    });

    $(".formCommentDisable").on("click", function (event) {
        event.preventDefault();
        
            var id = $(this).val();
            values = { "idCommentDisable": id };
            $.ajax({
                type: "POST",
                url: "/commentDisable",
                data: values,
                datatype: "json",
                success: function (response) {
                    response = JSON.parse(response);
                    $("#alertComment").html(response.message);
                    $("#alertComment").show("2000");
                    $("#disable").modal("hide");
                    $("#comment-"+ id).hide("slow");
                },
                error: function (response) {
                    $("#alertComment").text("Une erreur inattendue est survenue...");
                },
            });
        

    });

    $(".formCommentDelete").on("click", function (event) {
        event.preventDefault();
        
            var id = $(this).val();
            values = { "idCommentDelete": id };
            $.ajax({
                type: "POST",
                url: "/commentDelete",
                data: values,
                datatype: "json",
                success: function (response) {
                    response = JSON.parse(response);
                    $("#alertComment").html(response.message);
                    $("#alertComment").show("2000");
                    $("#delete").modal("hide");
                    $("#comment-"+ id).hide("slow");
                    
                },
                error: function (response) {
                    $("#alertComment").text("Une erreur inattendue est survenue...");
                },
            });
        

    });

    $("#alertCommentEdit").hide();
    $("#formCommentEdit").on("submit", function (event) {
        event.preventDefault();
        var form = $("#formCommentEdit");
        var values = form.serialize();
        $.ajax({
            type: "POST",
            url: "/commentEdit",
            data: values,
            datatype: "json",
            success: function (response) {
                response = JSON.parse(response);
                $("#alertCommentEdit").html(response.message);
                $("#alertCommentEdit").show("2000");
                if (response.success == true) {
                    setTimeout(function () {
                        window.location.href = "/commentManager?status="+response.status;
                    }, 2000)
                }
            },
            error: function (response) {
                $("#alertCommentEdit").text("Une erreur inattendue est survenue...");
            },
        });
    });

    $("#alertCategoryAdd").hide();
    $("#formCategoryAdd").on("submit", function (event) {
        event.preventDefault();
        var form = $("#formCategoryAdd");
        var values = form.serialize();
        $.ajax({
            type: "POST",
            url: "/categoryAdd",
            data: values,
            datatype: "json",
            success: function (response) {
                response = JSON.parse(response);
                $("#alertCategoryAdd").html(response.message);
                $("#alertCategoryAdd").show();

                if (response.success == true) {
                    $("#formCategoryAdd")[0].reset();
                    location.reload();
                }
            },
            error: function (response) {
                $("#alertCategoryAdd").text("Une erreur inattendue est survenue...");
            },
        });
    });

    $(".formCategoryDelete").on("click", function (event) {
        event.preventDefault();
        
            var id = $(this).val();
            values = { "idCategoryDelete": id };
            $.ajax({
                type: "POST",
                url: "/categoryDelete",
                data: values,
                datatype: "json",
                success: function (response) {
                    response = JSON.parse(response);
                    $("#alertCategory").html(response.message);
                    $("#alertCategory").show("2000");
                    $("#delete").modal("hide");
                    $("#category-"+ id).hide("slow");
                    
                },
                error: function (response) {
                    $("#alertCategory").text("Une erreur inattendue est survenue...");
                },
            });
        

    });

    $("#alertCategoryEdit").hide();
    $("#formCategoryEdit").on("submit", function (event) {
        event.preventDefault();
        var form = $("#formCategoryEdit");
        var values = form.serialize();
        $.ajax({
            type: "POST",
            url: "/categoryEdit",
            data: values,
            datatype: "json",
            success: function (response) {
                response = JSON.parse(response);
                $("#alertCategoryEdit").html(response.message);
                $("#alertCategoryEdit").show("2000");
                if (response.success == true) {
                    setTimeout(function () {
                        window.location.href = "/categoryManager";
                    }, 2000)
                }
            },
            error: function (response) {
                $("#alertCategoryEdit").text("Une erreur inattendue est survenue...");
            },
        });
    });

    $("#alertUser").hide();
    $(".formUserValidate").on("click", function (event) {
        event.preventDefault();

            var id = $(this).val();
            values = { "idUserValidate": id };
            $.ajax({
                type: "POST",
                url: "/userValidate",
                data: values,
                datatype: "json",
                success: function (response) {
                    response = JSON.parse(response);
                    $("#alertUser").html(response.message);
                    $("#alertUser").show("2000");
                    $("#validate").modal("hide");
                    location.reload();
                },
                error: function (response) {
                    $("#alertUser").text("Une erreur inattendue est survenue...");
                },
            });
        

    });

    $(".formUserDisable").on("click", function (event) {
        event.preventDefault();

            var id = $(this).val();
            values = { "idUserDisable": id };
            $.ajax({
                type: "POST",
                url: "/userDisable",
                data: values,
                datatype: "json",
                success: function (response) {
                    response = JSON.parse(response);
                    $("#alertUser").html(response.message);
                    $("#alertUser").show("2000");
                    $("#disable").modal("hide");
                    location.reload();
                    
                },
                error: function (response) {
                    $("#alertUser").text("Une erreur inattendue est survenue...");
                },
            });
        

    });

    $(".formUserDelete").on("click", function (event) {
        event.preventDefault();
        
            var id = $(this).val();
            values = { "idUserDelete": id };
            $.ajax({
                type: "POST",
                url: "/userDelete",
                data: values,
                datatype: "json",
                success: function (response) {
                    response = JSON.parse(response);
                    $("#alertUser").html(response.message);
                    $("#alertUser").show("2000");
                    $("#delete").modal("hide");
                    if(response.success==true)
                    {
                        $("#user-"+ id).hide("slow");
                    }
                    
                    
                },
                error: function (response) {
                    $("#alertUser").text("Une erreur inattendue est survenue...");
                },
            });
        

    });

    

    

});



