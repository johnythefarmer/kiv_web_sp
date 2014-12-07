angular.module('admin', [])
	.controller('adminCont', ['$scope', '$http', function($scope, $http) {
		$scope.defaultRevir = {"id" : 0, "rekaId" : 0, "cislo" : 0, "podrevir" : 0, "moId" : 0};
		$scope.revir = angular.copy($scope.defaultRevir);
		$scope.showEdit = false;
		$scope.showCreate = true;					

		$http.post('/lib/admin/fsg.php', {'method' : "select"}).success(function(response){
			if(response.success){
				$scope.reviry = response.reviry;
				$scope.mo = response.mo;
				$scope.reky = response.reky;
			} else {
				alert(response.message);
				window.location.replace("/"); 
			}
		});



		$scope.editShow = function($x) {
			$scope.showCreate = false;
			$scope.showEdit = true;
			$scope.revirEdit = angular.copy($x);
		};

		$scope.edit = function($x){
			if($x.id == null || $x.podrevir == null || $x.cislo == null || $x.rekaId == 0 || $x.moId == 0){
				alert("nevyplněny všechny údaje");
			}else {							
				$http.post('/lib/admin/fsg.php', {'method' : 'edit', 'revir' : $x}).success(function(response) {
					if(response.success){
						$scope.reviry = response.reviry;
						alert(response.message);
						$scope.showEdit = false;
						$scope.showCreate = true;
						$scope.revir = angular.copy($scope.defaultRevir);
					}else{
						alert(response.message);
					}
				});
			}
		};

		$scope.createShow = function() {
			$scope.showCreate = true;
			$scope.showEdit = false;
			$scope.revir = angular.copy($scope.defaultRevir);
		};

		$scope.delete = function($x){
			if(confirm("Opravdu si přejete tento revír smazat?")){
				$http.post('/lib/admin/fsg.php', {'method' : 'delete', 'revir' : $x}).success(function(response) {
					if(response.success){
						alert(response.message);
						$scope.reviry = response.reviry;
					}else{
						alert(response.message);
					}
				});
			}

		};

		$scope.create = function($x){
			if($x.id == null || $x.podrevir == null || $x.cislo == null || $x.rekaId == 0 || $x.moId == 0){
				alert("nevyplněny všechny údaje");
			}else {							
				$http.post('/lib/admin/fsg.php', {'method' : 'create', 'revir' : $x}).success(function(response) {
					if(response.success){
						alert(response.message);
						$scope.reviry = response.reviry;
						$scope.revir = angular.copy($scope.defaultRevir);
					}else{
						alert(response.message);
					}
				});
			}
		};
}]);