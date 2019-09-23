<template>
	<view>
		<vrow class="content" v-for="item in article_list">
		    <vcol span="100">
			    <navigator v-bind:url="'detail?titleid='+item.article_id" open-type="navigate">
				  <image class="img" v-bind:src="item.thumb"></image>
				</navigator>
		    </vcol>				  
		    <vcol span="70">
		        <vrow retract="1">{{item.title}}</vrow>
				<vrow class="subtitle" retract="1">{{item.house_type}} | {{item.area}} | {{item.price}}万</vrow>
		    </vcol>
			<vcol span="30">
			    <vrow retract="1"><button class="button" @click="callphone(item.phone)">预约设计</button></vrow>
			</vcol>
		</vrow>
		<vrow v-show="liststatus">
			 <vcol class="nocontent" span='100'>网络正忙，请稍候...</vcol>
		</vrow>
	</view>
</template>

<script>
	import vrow from '@/components/lml-layout/row.vue'
	import vcol from '@/components/lml-layout/col.vue'
	
	export default {
		components:{
			vrow,vcol
		},
		data() {
			return {
		      article_list:[],
			  liststatus:false
			}
		},
		onLoad() {
			    //默认加载文章列表
			     var self=this;
				 uni.request({
					url:'/dpc/index/Index/index',
					method:'GET',
					success:function(res){
						if(res.data.length!=0){
						    self.article_list=res.data;
						}else{
							self.liststatus=true;
						}
					}
				 })         
		},
		methods: {
               //拨打客户电话
			   callphone(phone){
				   uni.makePhoneCall({
				       phoneNumber: phone 
				   });
			   }
		}
	}
</script>

<style>
	.content{
		font-size: 32upx;
		margin: 0 0 15upx 0;
	}
	.img {
		width: 100%;
	}
	.subtitle{
		font-size: 28upx;
		color:#999999;
		margin-top: 10upx;
	}
	.button{
		background:-webkit-linear-gradient(left,#695648,#82858A); /* Safari 5.1 - 6.0 */
		background:linear-gradient(left,#695648,#82858A);/*chrom*/
		background:-ms-linear-gradient(left,#695648,#82858A);/* IE 10 */
		background:-moz-linear-gradient(left,#695648,#82858A);/*火狐*/ 
		background:-o-linear-gradient(left,#695648,#82858A); /* Opera 11.1 - 12.0 */
		color:#fff;
		margin-top: 10upx;
		font-size: 30upx;
	}
	.nocontent{
		margin-top:60upx;
		text-align: center;
		font-size: 30upx;
	}
</style>
