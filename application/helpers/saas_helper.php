<?php

function store_module(){
    return false;
  }

function special_access(){
	if(is_admin()){
		return true;
	}
	else if(is_store_admin()){
		if(store_module()){
			return false;
		}
		return true;
	}
}