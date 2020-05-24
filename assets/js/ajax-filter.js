jQuery(document).ready(function ($){
// Обработка добавлених елементов
var StatusSend = 'false';
//Scroll to TOP
  	function ScrollToResult () {
		$('html, body').animate({ scrollTop: $('.contetnt-loop').offset().top }, 500); 
  	}

  // Ajax Send Data

  	function AjaxSend (param, action) {
		var TypeData = 'html';
		// Проверка типа получеемих данных
   
		$.ajax({
	  		url: php_array1.admin_ajax,
	  		type:'POST',
	  		dataType:TypeData,
			data: ({
				'action': action,               
				'var': param,
			}),             
			beforeSend: function () {                
							$('.contetnt-loop').html('');
							$('.contetnt-loop').addClass('fa-spinner');
							$('.contetnt-loop').addClass('fa');
							$('.contetnt-loop').addClass('fa-spin');
							$('.contetnt-loop').addClass('fa-5x');                      
							$('.contetnt-loop').addClass('custom-spin');
							$('.alm-btn-wrap').hide();                                   
					  
	 		},
			success: function (data, textStatus, jqXHR) {
				
				$('#result').append('');
				$('.contetnt-loop').removeClass('fa-spinner');
				$('.contetnt-loop').removeClass('fa');
				$('.contetnt-loop').removeClass('fa-spin');
				$('.contetnt-loop').removeClass('fa-5x'); 
				$('.contetnt-loop').removeClass('custom-spin');
				$('.contetnt-loop').html('');
				ScrollToResult();
				// StatusSend = 'false';
				$( '.contetnt-loop' ).append( data ); 
	 		},
	  		error: function (jqXHR, textStatus, errorThrown) {
	   			console.log(jqXHR + " :: " + textStatus + " :: " + errorThrown);
	  		}
		});
	  }
	  
	// Функция поиска в шапке, результати буду подгружатся после ввода трех символов
	
	var searchParam = {
		action : 'global_seach',
	}
	$('.main-seach').bind('keyup focusin',  function(e) {
		if ($('.main-seach').val().length >= 3 ) {
			var sendSeach = {};
			sendSeach.StrToSeach  = $('.main-seach').val();

			AjaxGlobalsSeach(sendSeach, searchParam.action);
		};
	});


  	// Глобальний поиск по сайту 
	function AjaxGlobalsSeach (param, action) {
		var TypeData = 'html';

		// Проверка типа получеемих данных
	
		$.ajax({
			url: php_array1.admin_ajax,
			type:'POST',
			dataType:TypeData,
			data: ({
				'action': action,
				'var': param,
			}),             
			beforeSend: function () { 
				$('#seach-result').html('');
				$('.load-seach').removeClass('fa-search');
				$('.load-seach').addClass('fa-spin');
				$('.load-seach').addClass('fa-spinner');
			},
			success: function (data, textStatus, jqXHR) {
				if(param.isMobile) {
					$( '#'+param.id ).html('');
					$( '#'+param.id  + '-wrap').css({'max-width' : window.screen.availWidth - 10, 'display' : 'block'})
					$( '#'+param.id ).html( data ); 
				}
				else {
					$('#seach-result').html('');
					$('.load-seach').removeClass('fa-spinner');
					$('.load-seach').removeClass('fa-spin');
					$('.load-seach').addClass('fa-search');
					
					$( '#seach-result' ).append( data ); 
				}
				
			},
			error: function (jqXHR, textStatus, errorThrown) {
				console.log(jqXHR + " :: " + textStatus + " :: " + errorThrown);
			}
		});
  	}

  	window.searchGlobal = function (evt) {
		if( $(evt.target).val().length > 3 ) {			
			var sendSeach = {};
			sendSeach.StrToSeach  = $(evt.target).val();
			sendSeach.isMobile = true;
			sendSeach.id = $(evt.target).data('idres');

			AjaxGlobalsSeach(sendSeach, searchParam.action);
		}
		else {
			$( '#seach-result-mobile-wrap').css({'display' : 'none'});
		}
  	}


// Change Category

		$("#category").change(function(e) {
		  if($(this).val() == 0) return false;
			$('#result').text('');

			var sendWisth = $('.mrg-tb').width();
			 
		   
		   var param = {};
		   param.autor = $('input[name="author"]').val();
		   param.category = $('#category option:selected').val();
		   // param.width = WidthScreen(sendWisth);

		//    console.log($('#category option:selected').val())
			$('#TitleName').empty();

		   var action = 'my_seach';

		   AjaxSend(param, action); 
		   LoadAutors(param)
		
		});

   // Load Autors

   function LoadAutors (param){
	var action = 'select_autor';

	AjaxSend(param, action);
   }
   
   // Change Autors

   function QueryBooksAutor (param) {

	var action = 'my_seach';

	AjaxSend(param, action); 

   }

// Основной фильтр поиска на главной странице
// Функция отправки данных на сервере чекрез ajax

function sendAjaxToServer(paramToSendServer) {

}



// обработка допольнительных параметров поиска (paramSelect проверяет статус активности чекбокса активации опций)
function activeFormOther (paramSelect) {
 
  if (paramSelect === 'true') {

	
	$("input[name='blankRadio']").prop('disabled', false);
  };   
 
  if (paramSelect == 'false') {
   
	$("input[name='blankRadio']").prop({disabled: true});
  };
  
};


   $('#main-seach').on('change select ', 'select, input', 'her' , function(event) {

		// CONSTANT  id
	var CATEGORY = 'category-main';
	var STATUSBOOK = 'status-book';
	var LANGUAGE = 'language';
	var CHOOSEBOOK = 'choose-book';
	var OTHERPARAM = 'other-parameter';
	// console.log(event);
	// console.log(event.currentTarget.id); 
	//

	// Отправка запроса при нажатии
	$('#send-data-button').click(function(e) {
		if (StatusSend = 'false') {
		  StatusSend = 'true';
			e.preventDefault();
			var action = 'my_seach';
			var newDataSend = {};

			newDataSend.category = $('#category-main option:selected').val();
			newDataSend.statusbook = $('#status-book option:selected').val();
			newDataSend.language = $('#language option:selected').val();


			if ($("#send-links").attr("checked") == 'checked') {
			  newDataSend.selectToLink = 'true';
			};

			//  console.log(newDataSend);  
		   
		   AjaxSend(newDataSend, action);

			// AjaxSend(newDataSend, action);
		};
	  });


	// Получаем id формы на котором произошло соботие
	var EventMain = event.currentTarget.id;    

	if (EventMain == CATEGORY) {
	  // param.autor = $('#AutorName option:selected').text();
	//   console.log($('#category-main option:selected').text());
	  

	  $("#status-book").prop('disabled', false);        
	 
	};


	if (EventMain == STATUSBOOK) {
	  // param.autor = $('#AutorName option:selected').text();
	  // console.log($('#status-book option:selected').text());        

	  $("#language").prop('disabled', false);       
	 
	};

	if (EventMain == LANGUAGE) {
	  // param.autor = $('#AutorName option:selected').text();
	  // console.log($('#status-book option:selected').text());        

	  $("#send-data-button").prop('disabled', false);       
	 
	};
	// console.log($('#inlineCheckbox1').attr("checked") == 'checked');
	if ($("#inlineCheckbox1").attr("checked") == 'checked')  {

	 pramToSend = 'true';
	 activeFormOther(pramToSend);
	 
	};
	 if ($("#inlineCheckbox1").attr("checked") != 'checked')  {
	
	 
	 pramToSend = 'false';
	 activeFormOther(pramToSend);
	};
  });

  // Генерация ссилок на скачивание
  function getUrlVars()
  {
	  var vars = [], hash;
	  var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
	  for(var i = 0; i < hashes.length; i++)
	  {
		  hash = hashes[i].split('=');
		  vars.push(hash[0]);
		  vars[hash[0]] = hash[1];
	  }
	  return vars;
  }
var id = getUrlVars()["count"];
var key = getUrlVars()["key"];
var count = {};
count.id = id;
count.key = key;

var countdown = $('#countdown span'),
	but = $('button'),
	timer;
if (count.id == "" || !count.id) {
	$('#link').html( '<h3>Неверная ссылка на скачивания</h3><br><a href=" http://webbooks.com.ua">На главную</a>' );
  
  return false; 
  }
else
 {
  function startCountdown(){
	  var startFrom = 20;
	  countdown.text(startFrom).parent('p').show();
	  but.hide();
	  timer = setInterval(function(){
		  countdown.text(--startFrom);
		  if(startFrom <= 0) {
			  clearInterval(timer);
			  countdown.text('Сейчас появится ссылка');
			  $.ajax({
				url: php_array1.admin_ajax,
				type:'POST',
				dataType:'html',
				data: ({
				  'action': 'return_link_to_book',               
				  'count': count,
				}),             
				beforeSend: function () {
				  $('#link').html('<i class="fa fa-spinner fa-pulse"></i>');
			   },
				success: function (data, textStatus, jqXHR) {
				  $('#link').html('');
				//   console.log (data);   
				  $( '#link' ).append( data ); 
			   },
				error: function (jqXHR, textStatus, errorThrown) {
				 console.log(jqXHR + " :: " + textStatus + " :: " + errorThrown);
				}
			  });            
			  but.show();
		  }
	  },1000);
  }
 }
;

startCountdown();
});
