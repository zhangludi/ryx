/**
 * [公用函数]
 * @auth       ljj
 * @createdate 2016-11-25
 */
(function() {
	window.D = {
		link: "http://www.schoo1.com/Api",
		member_id: JSON.parse(localStorage.getItem('member_id')),
		/**
		 * setItem 本地存储
		 * @auth       liyang
		 * @createdate 2016-11-25
		 * @param      nameSpace 即key
		 * @param      data      即value
		 * @return
		 */
		setItem: function(nameSpace, data) {
			//存取操作
			if (data) {
				localStorage.setItem(nameSpace, JSON.stringify(data));
			}
			return (nameSpace && localStorage.getItem(nameSpace)) || null;
		},
		/**
		 * getItem 本地存储中根据key取值
		 * @auth       ljj
		 * @createdate 2016-11-25
		 * @param      nameSpace 即key
		 * @return     根据key取到的value
		 */
		getItem: function(nameSpace) {
			return JSON.parse(localStorage.getItem(nameSpace)) || null;
		},
		/**
		 * removeItem   删除本地存储
		 * @auth       ljj
		 * @createdate 2016-11-25
		 * @param      nameSpace 即key
		 * @return     清除结果
		 */
		removeItem: function(nameSpace) {
			return JSON.parse(localStorage.removeItem(nameSpace)) || null;
		},
		/**
		 * clear       删除本地存储
		 * @auth       ljj
		 * @createdate 2016-11-25
		 * @param      null
		 * @return     清除结果
		 */
		clear: function() {
			return JSON.parse(localStorage.clear()) || null;
		}
	}
})()